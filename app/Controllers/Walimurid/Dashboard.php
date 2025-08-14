<?php
namespace App\Controllers\WaliMurid;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\TagihanSppSiswaModel;
use App\Models\UangSakuModel;
use App\Models\TransaksiUangSakuModel;

class Dashboard extends BaseController
{
    protected $siswaModel;
    protected $tagihanSppSiswaModel;
    protected $uangSakuModel;
    protected $transaksiUangSakuModel;

    public function __construct()
    {
        $this->siswaModel = model(SiswaModel::class);
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
        $this->uangSakuModel = model(UangSakuModel::class);
        $this->transaksiUangSakuModel = model(TransaksiUangSakuModel::class);
    }

    public function index()
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();

        if (!$siswa) {
            return view('walimurid/dashboard', [
                'sisaTanggungan' => 0,
                'sisaUangSaku' => 0,
                'waliDari' => 'Data siswa tidak ditemukan',
                'daftarTanggunganSiswa' => [],
                'riwayatPengambilanUangSaku' => []
            ]);
        }

        $nis = $siswa['nis'];

        // Query untuk menghitung total sisa tanggungan
        $tanggunganData = $this->tagihanSppSiswaModel
            ->select('SUM(tagihan_spp.nominal - tagihan_spp_siswa.jumlah_bayar) as sisa_tanggungan')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.nis', $nis)
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // <-- TAMBAHKAN FILTER INI
            ->first();
        $sisaTanggungan = ($tanggunganData && isset($tanggunganData['sisa_tanggungan'])) ? (int)$tanggunganData['sisa_tanggungan'] : 0;

        $uangSaku = $this->uangSakuModel->find($nis);
        $sisaUangSaku = $uangSaku ? $uangSaku['saldo'] : 0;
        $waliDari = $siswa['nama_siswa'];

        // Query untuk menampilkan daftar tanggungan
        $daftarTanggunganBelumLunas = $this->tagihanSppSiswaModel
            ->select('
                tagihan_spp_siswa.id AS id_tagihan_siswa, 
                tagihan_spp.nama_tagihan, 
                tagihan_spp.jatuh_tempo, 
                tagihan_spp.nominal AS nominal_tagihan,
                tagihan_spp_siswa.jumlah_bayar
            ')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.nis', $nis)
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // <-- TAMBAHKAN FILTER INI
            ->orderBy('tagihan_spp.jatuh_tempo', 'ASC') 
            ->findAll(6);

        $riwayatPengambilanUangSaku = $this->transaksiUangSakuModel
            ->where('nis', $nis)
            ->where('tipe_transaksi', 'Keluar')
            ->orderBy('tanggal', 'DESC')
            ->findAll(6);

        return view('walimurid/dashboard', [
            'sisaTanggungan' => $sisaTanggungan,
            'sisaUangSaku' => $sisaUangSaku,
            'waliDari' => $waliDari,
            'daftarTanggunganSiswa' => $daftarTanggunganBelumLunas,
            'riwayatPengambilanUangSaku' => $riwayatPengambilanUangSaku
        ]);
    }
}