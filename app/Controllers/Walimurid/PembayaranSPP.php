<?php
// C:\xampp\htdocs\payment-gateway\app\Controllers\Walimurid\PembayaranSPP.php
namespace App\Controllers\WaliMurid;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\TagihanSppSiswaModel;
use App\Models\PembayaranSppModel;
use App\Models\TagihanModel;
use Config\MidtransConfig;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranSPP extends BaseController
{
    protected $siswaModel;
    protected $tagihanSppSiswaModel;
    protected $pembayaranSppModel;
    protected $tagihanModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->tagihanSppSiswaModel = new TagihanSppSiswaModel();
        $this->pembayaranSppModel = new PembayaranSppModel();
        $this->tagihanModel = new TagihanModel();

        $midtransConfig = new MidtransConfig();
        Config::$serverKey = $midtransConfig->serverKey;
        Config::$isProduction = $midtransConfig->isProduction;
        Config::$isSanitized = $midtransConfig->isSanitized;
        Config::$is3ds = $midtransConfig->is3ds;
    }

    public function index()
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) {
            return redirect()->to('walimurid/dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }
        $nis = $siswa['nis'];
        $selectedTagihanId = $this->request->getGet('selected_tagihan_id');

        $tagihanListRaw = $this->tagihanSppSiswaModel
        ->select('tagihan_spp.id as tagihan_id, tagihan_spp.nama_tagihan, tagihan_spp.jatuh_tempo, tagihan_spp.nominal, tagihan_spp_siswa.status, tagihan_spp_siswa.jumlah_bayar')
        ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
        ->where('tagihan_spp_siswa.nis', $nis)
        ->where('tagihan_spp_siswa.status', 'Belum Lunas')
        ->where('tagihan_spp.status_tagihan', 'Aktif') 
        ->orderBy('tagihan_spp.jatuh_tempo', 'ASC')
        ->findAll();
        
        $tagihanList = array_map(function($tagihan) {
            $jumlahBayar = $tagihan['jumlah_bayar'] ?? 0; 
            $tagihan['jumlah_bayar'] = $jumlahBayar;
            $tagihan['sisa_tagihan'] = (int)$tagihan['nominal'] - (int)$jumlahBayar;
            return $tagihan;
        }, $tagihanListRaw);

        return view('walimurid/pembayaran_spp', [
            'nis' => $nis,
            'nama_siswa' => $siswa['nama_siswa'],
            'kelas' => $siswa['kelas'],
            'tagihanList' => $tagihanList,
            'selected_tagihan_id' => $selectedTagihanId
        ]);
    }

    public function prosesPembayaranSPP()
    {
        $post = $this->request->getPost();
        $tagihanId = $post['nama_tagihan'];
        $jumlahDibayarBaru = (int) $post['jumlah_dibayar'];
        $nis = $post['nis'];

        if (empty($tagihanId) || $jumlahDibayarBaru <= 0) {
            return redirect()->back()->withInput()->with('error', 'Tagihan dan jumlah bayar harus valid.');
        }
        
        $tagihanSiswa = $this->tagihanSppSiswaModel
            ->where('nis', $nis)
            ->where('tagihan_id', $tagihanId)
            ->first();
        
        $detailTagihan = $this->tagihanModel->find($tagihanId);

        if (!$tagihanSiswa || !$detailTagihan) {
            return redirect()->back()->with('error', 'Tagihan tidak valid atau tidak ditemukan.');
        }

        $sisaTagihanServer = (int)$detailTagihan['nominal'] - (int)$tagihanSiswa['jumlah_bayar'];

        if ($jumlahDibayarBaru > $sisaTagihanServer) {
            return redirect()->back()->withInput()->with('error', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan sebesar Rp ' . number_format($sisaTagihanServer, 0, ',', '.'));
        }

        $pembayaranPending = $this->pembayaranSppModel
            ->where('tagihan_spp_id', $tagihanId)->where('nis', $nis)->where('status', 'Pending')->first();
            
        // Cek detail tagihan (sudah ada di atas, tidak perlu query lagi)
        if (!$detailTagihan) {
            return redirect()->back()->with('error', 'Detail tagihan tidak ditemukan.');
        }
        
        if ($pembayaranPending) {
            if ((int)$pembayaranPending['jumlah_bayar'] === $jumlahDibayarBaru) {
                $snapToken = $pembayaranPending['snap_token'];
                $orderId = $pembayaranPending['midtrans_order_id'];
            } else {
                try {
                    Transaction::cancel($pembayaranPending['midtrans_order_id']);
                    $this->pembayaranSppModel->update($pembayaranPending['id'], ['status' => 'Gagal']);
                } catch (\Exception $e) {
                    log_message('error', 'Gagal membatalkan transaksi Midtrans lama: ' . $e->getMessage());
                    if (strpos($e->getMessage(), '404') !== false || strpos($e->getMessage(), 'not found') !== false) {
                        $this->pembayaranSppModel->update($pembayaranPending['id'], ['status' => 'Gagal']);
                    } else {
                        return redirect()->back()->withInput()->with('error', 'Gagal membatalkan pembayaran lama. Silakan coba lagi.');
                    }
                }
                $pembayaranPending = null; 
            }
        }

        if (!$pembayaranPending) {
            $orderId = 'SPP-' . $post['nis'] . '-' . time();
            $params = [
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => $jumlahDibayarBaru],
                'item_details' => [['id' => 'TAGIHAN-' . $tagihanId, 'price' => $jumlahDibayarBaru, 'quantity' => 1, 'name' => $detailTagihan['nama_tagihan']]],
                'customer_details' => ['first_name' => $post['nama_siswa'], 'email' => session()->get('user')['email']],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $this->pembayaranSppModel->insert([
                    'nis' => $post['nis'], 
                    'tagihan_spp_id' => $tagihanId, 
                    'jumlah_bayar' => $jumlahDibayarBaru,
                    'status' => 'Pending', 
                    'midtrans_order_id' => $orderId, 
                    'snap_token' => $snapToken,
                    'catatan' => $post['catatan'] ?? null 
                ]);
            } catch (\Exception $e) {
                log_message('error', '[Midtrans Snap Error] ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            }
        }

        return view('walimurid/payment_page', [
            'snapToken' => $snapToken, 'orderId' => $orderId, 'nis_siswa' => $post['nis'],
            'nama_siswa_display' => $post['nama_siswa'], 'kelas_siswa' => $post['kelas'],
            'nama_tagihan_display' => $detailTagihan['nama_tagihan'], 'jatuh_tempo_display' => $detailTagihan['jatuh_tempo'],
            'nominal_asli_tagihan' => $detailTagihan['nominal'], 'jumlah_yang_dibayar' => $jumlahDibayarBaru, 
            'catatan_display' => $post['catatan'] ?? '',
        ]);
    }
    
    public function batalPembayaran($orderId)
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();

        if (!$siswa) {
            return redirect()->to('walimurid/dashboard')->with('error', 'Akses tidak sah.');
        }

        $pembayaran = $this->pembayaranSppModel
            ->where('midtrans_order_id', $orderId)
            ->where('nis', $siswa['nis'])
            ->first();

        if (!$pembayaran || $pembayaran['status'] !== 'Pending') {
            return redirect()->to('walimurid/tagihan_spp')->with('error', 'Pembayaran tidak ditemukan atau statusnya tidak valid untuk dibatalkan.');
        }

        try {
            Transaction::cancel($orderId);
            $this->pembayaranSppModel->update($pembayaran['id'], ['status' => 'Gagal']);
            session()->setFlashdata('success', 'Proses pembayaran untuk Order ID ' . esc($orderId) . ' telah berhasil dibatalkan.');
        } catch (\Exception $e) {
            log_message('error', 'Gagal membatalkan transaksi Midtrans secara manual (Order ID: '.$orderId.'): ' . $e->getMessage());
            if (strpos($e->getMessage(), '404') !== false || strpos($e->getMessage(), 'not found') !== false) {
                 $this->pembayaranSppModel->update($pembayaran['id'], ['status' => 'Gagal']);
                 session()->setFlashdata('info', 'Transaksi tidak ditemukan di Midtrans dan telah ditandai Gagal di sistem kami.');
            } else {
                 session()->setFlashdata('error', 'Gagal membatalkan pembayaran di Midtrans. Silakan coba lagi.');
            }
        }

        return redirect()->to('walimurid/tagihan_spp');
    }
}
