<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuthModel;
use App\Models\SiswaModel;
use App\Models\TagihanModel;
use App\Models\TagihanSppSiswaModel;
use App\Models\PembayaranSppModel;
use App\Models\TransaksiUangSakuModel;

class Dashboard extends BaseController
{
    protected $authModel;
    protected $siswaModel;
    protected $tagihanModel;
    protected $tagihanSppSiswaModel;
    protected $pembayaranSppModel;
    protected $transaksiUangSakuModel;

    public function __construct()
    {
        $this->authModel = model(AuthModel::class);
        $this->siswaModel = model(SiswaModel::class);
        $this->tagihanModel = model(TagihanModel::class);
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
        $this->pembayaranSppModel = model(PembayaranSppModel::class);
        $this->transaksiUangSakuModel = model(TransaksiUangSakuModel::class);
    }

    public function index()
    {
        $user = $this->authModel->find(session()->get('user')['id']);
        $totalSiswa = $this->siswaModel->countAllResults();
        
        $bulanIni = date('Y-m');
        $rekapData = $this->pembayaranSppModel
            ->selectSum('jumlah_bayar', 'total_bayar')
            // Kita tidak perlu join karena hanya butuh total pembayaran yang sukses bulan ini
            ->where('pembayaran_spp.status', 'Sukses')
            ->like('pembayaran_spp.tanggal_bayar', $bulanIni . '%')
            ->first();
        $rekapBulanan = $rekapData['total_bayar'] ?? 0;
        
        $tanggunganData = $this->tagihanSppSiswaModel
            ->select('SUM(tagihan_spp.nominal - tagihan_spp_siswa.jumlah_bayar) AS total_tanggungan')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // <-- 2. Filter Total Tanggungan
            ->first();
        $jumlahTanggungan = $tanggunganData['total_tanggungan'] ?? 0;

        // Query untuk daftar tagihan di dashboard
        $sppAktifList = $this->tagihanModel
            ->where('status_tagihan', 'Aktif') // <-- 3. Filter Daftar SPP Aktif
            ->orderBy('created_at', 'DESC')->findAll();

        // --- Data untuk Donut Chart ---
        $lunasData = $this->tagihanSppSiswaModel
            ->selectSum('jumlah_bayar', 'total_lunas')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id') // Join untuk filter
            ->where('tagihan_spp_siswa.status', 'Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // <-- 4. Filter Donut Chart (Lunas)
            ->first();
        $jumlahLunas = $lunasData['total_lunas'] ?? 0;

        // Data Sisa Tanggungan untuk Donut Chart sudah terfilter dari query $jumlahTanggungan di atas
        $jumlahBelumLunas = $jumlahTanggungan;

        $dicicilData = $this->tagihanSppSiswaModel
            ->selectSum('jumlah_bayar', 'total_dicicil')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id') // Join untuk filter
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp_siswa.jumlah_bayar >', 0)
            ->where('tagihan_spp.status_tagihan', 'Aktif') // <-- 5. Filter Donut Chart (Dicicil)
            ->first();
        $jumlahDicicil = $dicicilData['total_dicicil'] ?? 0;

        // Data Uang Saku (tidak perlu filter)
        $uangMasukData = $this->transaksiUangSakuModel->selectSum('nominal', 'total_masuk')->where('tipe_transaksi', 'Masuk')->first();
        $totalUangMasuk = $uangMasukData['total_masuk'] ?? 0;
        $uangKeluarData = $this->transaksiUangSakuModel->selectSum('nominal', 'total_keluar')->where('tipe_transaksi', 'Keluar')->first();
        $totalUangKeluar = $uangKeluarData['total_keluar'] ?? 0;

        return view('admin/dashboard', [
            'user' => $user,
            'totalSiswa' => $totalSiswa,
            'rekapBulanan' => $rekapBulanan,
            'jumlahTanggungan' => $jumlahTanggungan,
            'sppAktifList' => $sppAktifList,
            'jumlahLunas' => $jumlahLunas,
            'jumlahBelumLunas' => $jumlahBelumLunas,
            'jumlahDicicil' => $jumlahDicicil,
            'totalUangMasuk' => $totalUangMasuk,
            'totalUangKeluar' => $totalUangKeluar,
        ]);
    }
} 