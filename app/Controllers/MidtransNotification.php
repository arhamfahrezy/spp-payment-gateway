<?php
// C:\xampp\htdocs\payment-gateway\app\Controllers\MidtransNotification.php
namespace App\Controllers;

use Config\MidtransConfig;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\PembayaranSppModel;
use App\Models\TagihanSppSiswaModel;
use App\Models\PembayaranUangSakuModel;
use App\Models\TransaksiUangSakuModel;
use App\Models\UangSakuModel;
use App\Models\TagihanModel;

class MidtransNotification extends BaseController
{
    protected $pembayaranSppModel;
    protected $tagihanSppSiswaModel;
    protected $pembayaranUangSakuModel;
    protected $transaksiUangSakuModel;
    protected $uangSakuModel;
    protected $midtransLogModel;
    protected $tagihanModel;

    public function __construct()
    {
        $midtransConfig = new MidtransConfig();
        Config::$serverKey = $midtransConfig->serverKey;
        Config::$isProduction = $midtransConfig->isProduction;
        Config::$isSanitized = $midtransConfig->isSanitized;
        Config::$is3ds = $midtransConfig->is3ds;

        $this->pembayaranSppModel = new PembayaranSppModel();
        $this->tagihanSppSiswaModel = new TagihanSppSiswaModel();
        $this->pembayaranUangSakuModel = new PembayaranUangSakuModel();
        $this->transaksiUangSakuModel = new TransaksiUangSakuModel();
        $this->uangSakuModel = new UangSakuModel();
        $this->tagihanModel = new TagihanModel();
        $db = \Config\Database::connect();
        $this->midtransLogModel = $db->table('midtrans_log');
    }

    public function index()
    {   $notif = new Notification();
        $this->midtransLogModel->insert([
            'order_id' => $notif->order_id, 'status_code' => $notif->status_code,
            'gross_amount' => $notif->gross_amount, 'transaction_status' => $notif->transaction_status,
            'payment_type' => $notif->payment_type, 'json_response' => json_encode($notif),
        ]);
        if (strpos($notif->order_id, 'SPP-') === 0) {
            $this->handleSppPaymentNotification($notif);
        } elseif (strpos($notif->order_id, 'USAKU-') === 0) {
            $this->handleUangSakuPaymentNotification($notif);
        }   return $this->response->setStatusCode(200);
    }
    private function handleSppPaymentNotification($notif)
    {   $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status;
        $pembayaran = $this->pembayaranSppModel->where('midtrans_order_id', $orderId)->first();
        if (!$pembayaran) return;
        $statusUpdate = '';
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'accept') $statusUpdate = 'Sukses';
        } elseif ($transactionStatus == 'pending') {
            $statusUpdate = 'Pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $statusUpdate = 'Gagal';
        }

        if ($statusUpdate && $pembayaran['status'] !== 'Sukses') {
            // HANYA UPDATE STATUS DI TABEL PEMBAYARAN_SPP
            $this->pembayaranSppModel->update($pembayaran['id'], ['status' => $statusUpdate]);

            // Jika pembayaran sukses, lanjutkan update tabel tagihan siswa
            if ($statusUpdate === 'Sukses') {
                $tagihanSiswa = $this->tagihanSppSiswaModel
                    ->where('nis', $pembayaran['nis'])
                    ->where('tagihan_id', $pembayaran['tagihan_spp_id'])
                    ->first();

                $tagihanInduk = $this->tagihanModel->find($pembayaran['tagihan_spp_id']);
                
                if ($tagihanSiswa && $tagihanInduk) {
                    $totalSudahDibayar = (int)$tagihanSiswa['jumlah_bayar'] + (int)$pembayaran['jumlah_bayar'];
                    $totalTagihan = (int)$tagihanInduk['nominal'];
                    $statusTagihanBaru = ($totalSudahDibayar >= $totalTagihan) ? 'Lunas' : 'Belum Lunas';

                    // MODIFIKASI: Logika di bawah ini sekarang akan bekerja dengan benar karena
                    // $pembayaran['catatan'] berisi catatan asli dari wali murid.
                    $catatanDariWaliMurid = $pembayaran['catatan'] ?: null;
                    
                    // Gabungkan dengan catatan sebelumnya jika ada
                    $catatanUpdate = $tagihanSiswa['catatan_walimurid'];
                    if ($catatanDariWaliMurid) {
                        // Tambahkan baris baru (\n) jika sudah ada catatan sebelumnya
                        $catatanUpdate = ($catatanUpdate ? $catatanUpdate . "\n" : "") . $catatanDariWaliMurid;
                    }

                    $this->tagihanSppSiswaModel->update($tagihanSiswa['id'], [
                        'status' => $statusTagihanBaru,
                        'jumlah_bayar' => $totalSudahDibayar,
                        'tanggal_bayar' => date('Y-m-d H:i:s'),
                        'catatan_walimurid' => $catatanUpdate,
                    ]);
                }
            }
        }
    }

    private function handleUangSakuPaymentNotification($notif)
    {
        // Logika uang saku tidak berubah
        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status;

        $pembayaran = $this->pembayaranUangSakuModel->where('midtrans_order_id', $orderId)->first();
        if (!$pembayaran) return;

        $statusUpdate = '';
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'accept') $statusUpdate = 'Sukses';
        } elseif ($transactionStatus == 'pending') {
            $statusUpdate = 'Pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $statusUpdate = 'Gagal';
        }

        if ($statusUpdate && $pembayaran['status'] !== 'Sukses') {
            $this->pembayaranUangSakuModel->update($pembayaran['id'], ['status' => $statusUpdate]);

            if ($statusUpdate === 'Sukses') {
                // Buat entri di riwayat transaksi
                $this->transaksiUangSakuModel->insert([
                    'nis' => $pembayaran['nis'],
                    'tipe_transaksi' => 'Masuk',
                    'nominal' => $pembayaran['jumlah_bayar'],
                    'tanggal' => date('Y-m-d H:i:s'),
                    'catatan' => $pembayaran['catatan_walimurid'] ?? 'Top Up via Midtrans', // Menggunakan catatan dari walimurid
                ]);

                // ======== PERBAIKAN DIMULAI DI SINI ========
                $nis = $pembayaran['nis'];
                $jumlahBayar = (int)$pembayaran['jumlah_bayar'];

                // 1. Cek apakah saldo untuk siswa ini sudah ada
                $uangSaku = $this->uangSakuModel->find($nis);

                if ($uangSaku) {
                    // 2. Jika ada, update saldo yang ada
                    $this->uangSakuModel->set('saldo', 'saldo + ' . $jumlahBayar, false)
                                        ->where('nis', $nis)
                                        ->update();
                } else {
                    // 3. Jika tidak ada, buat data saldo baru
                    $this->uangSakuModel->insert([
                        'nis' => $nis,
                        'saldo' => $jumlahBayar,
                    ]);
                }
                // ======== PERBAIKAN SELESAI ========
            }
        }
    }
}