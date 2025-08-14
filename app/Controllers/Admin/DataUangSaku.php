<?php
// app/Controllers/Admin/DataUangSaku.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UangSakuModel;
use App\Models\TransaksiUangSakuModel;
use App\Models\SiswaModel; // Diperlukan untuk join

class DataUangSaku extends BaseController
{
    protected $uangSakuModel;
    protected $transaksiUangSakuModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->uangSakuModel = new UangSakuModel();
        $this->transaksiUangSakuModel = new TransaksiUangSakuModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        // Jumlah Total Saldo Uang Saku Saat Ini (dari semua siswa)
        $queryJumlahSaldo = $this->uangSakuModel->selectSum('saldo', 'total_saldo')->first();
        $jumlahTotalSaldoUangSaku = $queryJumlahSaldo['total_saldo'] ?? 0;

        // Total Uang Saku Pernah Masuk (Historis)
        $queryTotalMasuk = $this->transaksiUangSakuModel
            ->selectSum('nominal', 'total_masuk')
            ->where('tipe_transaksi', 'Masuk')
            ->first();
        $totalUangSakuMasukHistoris = $queryTotalMasuk['total_masuk'] ?? 0;
        
        // Total Uang Saku Pernah Diambil (Historis)
        $queryTotalDiambil = $this->transaksiUangSakuModel
            ->selectSum('nominal', 'total_keluar')
            ->where('tipe_transaksi', 'Keluar')
            ->first();
        $totalUangSakuDiambilHistoris = $queryTotalDiambil['total_keluar'] ?? 0;

        // Data untuk Tab 1: Daftar Uang Saku per Siswa (saldo saat ini)
        $daftarUangSaku = $this->uangSakuModel
            ->select('uang_saku.nis, siswa.nama_siswa, siswa.kelas, uang_saku.saldo')
            ->join('siswa', 'siswa.nis = uang_saku.nis', 'left') // Left join agar siswa tanpa record uang saku tetap bisa muncul (jika diinginkan, atau inner join jika harus ada record uang saku)
            ->orderBy('siswa.nama_siswa', 'DESC')
            ->findAll();

        // Data untuk Tab 2: Riwayat Uang Saku Masuk
        $uangSakuMasukList = $this->transaksiUangSakuModel
            ->select('transaksi_uang_saku.id as transaksi_id, transaksi_uang_saku.nis, siswa.nama_siswa, siswa.kelas, transaksi_uang_saku.tanggal, transaksi_uang_saku.nominal, transaksi_uang_saku.catatan')
            ->join('siswa', 'siswa.nis = transaksi_uang_saku.nis', 'left')
            ->where('transaksi_uang_saku.tipe_transaksi', 'Masuk')
            ->orderBy('transaksi_uang_saku.tanggal', 'DESC')
            ->findAll();

        // Data untuk Tab 3: Riwayat Uang Saku Diambil (Keluar)
        $uangSakuDiambilList = $this->transaksiUangSakuModel
            ->select('transaksi_uang_saku.id as transaksi_id, transaksi_uang_saku.nis, siswa.nama_siswa, siswa.kelas, transaksi_uang_saku.tanggal, transaksi_uang_saku.nominal, transaksi_uang_saku.catatan')
            ->join('siswa', 'siswa.nis = transaksi_uang_saku.nis', 'left')
            ->where('transaksi_uang_saku.tipe_transaksi', 'Keluar')
            ->orderBy('transaksi_uang_saku.tanggal', 'DESC')
            ->findAll();

        return view('admin/data_uang_saku', [
            'jumlahTotalSaldoUangSaku' => $jumlahTotalSaldoUangSaku,
            'totalUangSakuMasukHistoris' => $totalUangSakuMasukHistoris,
            'totalUangSakuDiambilHistoris' => $totalUangSakuDiambilHistoris,
            'daftarUangSaku' => $daftarUangSaku,
            'uangSakuMasukList' => $uangSakuMasukList,
            'uangSakuDiambilList' => $uangSakuDiambilList,
        ]);
    }
    // FUNGSI BARU UNTUK CETAK TRANSAKSI UANG SAKU
    public function cetak_transaksi($transaksi_id)
    {
        $transaksi = $this->transaksiUangSakuModel
            ->select('transaksi_uang_saku.*, siswa.nama_siswa, siswa.kelas')
            ->join('siswa', 'siswa.nis = transaksi_uang_saku.nis', 'left')
            ->find($transaksi_id);

        if (!$transaksi) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        return view('admin/cetak_transaksi_uang_saku', ['transaksi' => $transaksi]);
    }
}