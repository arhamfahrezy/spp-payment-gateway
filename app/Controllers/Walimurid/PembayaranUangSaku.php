<?php
// C:\xampp\htdocs\payment-gateway\app\Controllers\Walimurid\PembayaranUangSaku.php
namespace App\Controllers\WaliMurid;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\PembayaranUangSakuModel;
use Config\MidtransConfig;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranUangSaku extends BaseController
{
    protected $siswaModel;
    protected $pembayaranUangSakuModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->pembayaranUangSakuModel = new PembayaranUangSakuModel();

        $midtransConfig = new MidtransConfig();
        Config::$serverKey = $midtransConfig->serverKey;
        Config::$isProduction = $midtransConfig->isProduction;
        Config::$isSanitized = $midtransConfig->isSanitized;
        Config::$is3ds = $midtransConfig->is3ds;
    }

    public function index()
    {
        $userId = session()->get('user')['id'];
        $siswaData = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswaData) {
            return redirect()->to('walimurid/dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // MODIFIKASI: Cek apakah ada pembayaran pending untuk diisi ke form
        $pendingPayment = $this->pembayaranUangSakuModel
            ->where('nis', $siswaData['nis'])
            ->where('status', 'Pending')
            ->first();

        return view('walimurid/pembayaran_uang_saku', [
            'title' => 'Top Up Uang Saku - ' . esc($siswaData['nama_siswa']),
            'nis' => $siswaData['nis'],
            'nama_siswa_view' => $siswaData['nama_siswa'],
            'pending_payment' => $pendingPayment // Kirim data pending ke view
        ]);
    }

    public function prosesPembayaran()
    {
        $post = $this->request->getPost();
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        $nominalBayarBaru = (int)($post['nominal'] ?? 0);

        if (!$siswa || $nominalBayarBaru <= 0) {
            return redirect()->back()->withInput()->with('error', 'Data siswa atau nominal tidak valid.');
        }

        $pembayaranPending = $this->pembayaranUangSakuModel
            ->where('nis', $siswa['nis'])->where('status', 'Pending')->first();

        if ($pembayaranPending) {
            if ((int)$pembayaranPending['jumlah_bayar'] === $nominalBayarBaru) {
                $snapToken = $pembayaranPending['snap_token'];
                $orderId = $pembayaranPending['midtrans_order_id'];
            } else {
                try {
                    Transaction::cancel($pembayaranPending['midtrans_order_id']);
                    $this->pembayaranUangSakuModel->update($pembayaranPending['id'], ['status' => 'Gagal']);
                } catch (\Exception $e) {
                    log_message('error', 'Gagal batal topup lama (Order ID: '.$pembayaranPending['midtrans_order_id'].'): ' . $e->getMessage());
                    if (strpos($e->getMessage(), '404') !== false || strpos($e->getMessage(), 'not found') !== false) {
                        $this->pembayaranUangSakuModel->update($pembayaranPending['id'], ['status' => 'Gagal']);
                    } else {
                        return redirect()->back()->withInput()->with('error', 'Gagal membatalkan top up lama. Coba lagi.');
                    }
                }
                $pembayaranPending = null;
            }
        }

        if (!$pembayaranPending) {
            $orderId = 'USAKU-' . $siswa['nis'] . '-' . time();
            $params = [
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => $nominalBayarBaru],
                'item_details' => [['id' => 'TOPUP-'.$siswa['nis'], 'price' => $nominalBayarBaru, 'quantity' => 1, 'name' => 'Top Up Uang Saku ' . $siswa['nama_siswa']]],
                'customer_details' => ['first_name' => $siswa['nama_siswa'], 'email' => session()->get('user')['email']],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $this->pembayaranUangSakuModel->insert([
                    'nis' => $siswa['nis'], 'jumlah_bayar' => $nominalBayarBaru, 'catatan_walimurid' => $post['catatan'],
                    'status' => 'Pending', 'midtrans_order_id' => $orderId, 'snap_token' => $snapToken,
                ]);
            } catch (\Exception $e) {
                log_message('error', '[Midtrans Snap UangSaku Error] ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal memproses top up: ' . $e->getMessage());
            }
        }

        return view('walimurid/uang_saku_konfirmasi_bayar', [
            'title' => 'Konfirmasi Top Up Uang Saku', 'snapToken' => $snapToken, 'orderId' => $orderId,
            'nis_siswa' => $siswa['nis'], 'nama_siswa_display' => $siswa['nama_siswa'],
            'jumlah_yang_dibayar' => $nominalBayarBaru, 'catatan_display' => $post['catatan'],
            'item_name_display' => 'Top Up Uang Saku',
        ]);
    }

    public function batalPembayaran($orderId)
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) { return redirect()->to('walimurid/dashboard')->with('error', 'Akses tidak sah.'); }

        $pembayaran = $this->pembayaranUangSakuModel->where('midtrans_order_id', $orderId)->where('nis', $siswa['nis'])->first();
        if (!$pembayaran || $pembayaran['status'] !== 'Pending') {
            return redirect()->to('walimurid/uang_saku')->with('error', 'Top up tidak ditemukan atau tidak bisa dibatalkan.');
        }

        try {
            Transaction::cancel($orderId);
            $this->pembayaranUangSakuModel->update($pembayaran['id'], ['status' => 'Gagal']);
            session()->setFlashdata('success', 'Proses top up untuk Order ID ' . esc($orderId) . ' berhasil dibatalkan.');
        } catch (\Exception $e) {
            log_message('error', 'Gagal batal topup manual: ' . $e->getMessage());
            if (strpos($e->getMessage(), '404') !== false || strpos($e->getMessage(), 'not found') !== false) {
                 $this->pembayaranUangSakuModel->update($pembayaran['id'], ['status' => 'Gagal']);
                 session()->setFlashdata('info', 'Transaksi tidak ditemukan di Midtrans dan telah ditandai Gagal di sistem kami.');
            } else {
                 session()->setFlashdata('error', 'Gagal membatalkan top up di Midtrans. Coba lagi.');
            }
        }
        return redirect()->to('walimurid/uang_saku');
    }
}
