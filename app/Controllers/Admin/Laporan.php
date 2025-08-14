<?php
// app/Controllers/Admin/Laporan.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TagihanSppSiswaModel;
use App\Models\TransaksiUangSakuModel;

class Laporan extends BaseController
{
    protected $tagihanSppSiswaModel;
    protected $transaksiUangSakuModel;

    public function __construct()
    {
        $this->tagihanSppSiswaModel = new TagihanSppSiswaModel();
        $this->transaksiUangSakuModel = new TransaksiUangSakuModel();
    }

    public function index()
    {
        $filter = [
            'periode_tipe_spp' => $this->request->getGet('periode_tipe_spp') ?? 'bulanan',
            'periode_bulan_spp' => $this->request->getGet('periode_bulan_spp') ?? date('Y-m'),
            'periode_tahun_spp' => $this->request->getGet('periode_tahun_spp') ?? date('Y'),
            'status_spp' => $this->request->getGet('status_spp') ?? 'Lunas',
            'search_spp' => $this->request->getGet('search_spp') ?? '',
            'periode_tipe_us' => $this->request->getGet('periode_tipe_us') ?? 'bulanan',
            'periode_bulan_us' => $this->request->getGet('periode_bulan_us') ?? date('Y-m'),
            'periode_tahun_us' => $this->request->getGet('periode_tahun_us') ?? date('Y'),
            'status_us' => $this->request->getGet('status_us') ?? '',
            'search_us' => $this->request->getGet('search_us') ?? '',
            'tab' => $this->request->getGet('tab') ?? 'spp',
        ];
        
        $laporanSPPQuery = $this->buildSppQuery($filter);
        $laporanSPP = $laporanSPPQuery->orderBy('tagihan_spp_siswa.updated_at', 'DESC')->findAll();
        
        $laporanUSQuery = $this->buildUangSakuQuery($filter);
        $laporanUangSaku = $laporanUSQuery->orderBy('transaksi_uang_saku.tanggal', 'DESC')->findAll();
        
        $data = [
            'title' => 'Laporan Keuangan',
            'laporanSPP' => $laporanSPP,
            'laporanUangSaku' => $laporanUangSaku,
            'filter' => $filter,
        ];
        return view('admin/laporan', $data);
    }

    public function cetak_spp()
    {
        $filter = [
            'periode_tipe_spp' => $this->request->getGet('periode_tipe_spp') ?? 'bulanan',
            'periode_bulan_spp' => $this->request->getGet('periode_bulan_spp') ?? date('Y-m'),
            'periode_tahun_spp' => $this->request->getGet('periode_tahun_spp') ?? date('Y'),
            'status_spp' => $this->request->getGet('status_spp') ?? '',
            'search_spp' => $this->request->getGet('search_spp') ?? '',
        ];
        
        $laporanSPPQuery = $this->buildSppQuery($filter);

        if ($filter['periode_tipe_spp'] === 'tahunan') {
            $periode_label = "Tahun " . $filter['periode_tahun_spp'];
        } else {
            $formatter = new \IntlDateFormatter('id_ID', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'MMMM yyyy');
            $periode_label = $formatter->format(strtotime($filter['periode_bulan_spp']));
        }

        $data = [
            'title' => 'Laporan Pembayaran SPP',
            'laporanSPP' => $laporanSPPQuery->orderBy('tagihan_spp_siswa.updated_at', 'DESC')->findAll(),
            'periode_label' => $periode_label,
        ];

        return view('admin/laporan_cetak_spp', $data);
    }

    public function cetak_uang_saku()
    {
        $filter = [
            'periode_tipe_us' => $this->request->getGet('periode_tipe_us') ?? 'bulanan',
            'periode_bulan_us' => $this->request->getGet('periode_bulan_us') ?? date('Y-m'),
            'periode_tahun_us' => $this->request->getGet('periode_tahun_us') ?? date('Y'),
            'status_us' => $this->request->getGet('status_us') ?? '',
            'search_us' => $this->request->getGet('search_us') ?? '',
        ];

        $laporanUSQuery = $this->buildUangSakuQuery($filter);

        if ($filter['periode_tipe_us'] === 'tahunan') {
            $periode_label = "Tahun " . $filter['periode_tahun_us'];
        } else {
            $formatter = new \IntlDateFormatter('id_ID', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'MMMM yyyy');
            $periode_label = $formatter->format(strtotime($filter['periode_bulan_us']));
        }

        $data = [
            'title' => 'Laporan Transaksi Uang Saku',
            'laporanUangSaku' => $laporanUSQuery->orderBy('transaksi_uang_saku.tanggal', 'DESC')->findAll(),
            'periode_label' => $periode_label,
        ];

        return view('admin/laporan_cetak_us', $data);
    }

    private function buildSppQuery(array $filter)
    {
        if ($filter['periode_tipe_spp'] === 'tahunan') {
            $tahun = $filter['periode_tahun_spp'];
            $startDate = "$tahun-01-01 00:00:00";
            $endDate = "$tahun-12-31 23:59:59";
        } else {
            $bulan = $filter['periode_bulan_spp'];
            $startDate = "$bulan-01 00:00:00";
            $endDate = date('Y-m-t', strtotime($startDate)) . " 23:59:59";
        }

        $query = $this->tagihanSppSiswaModel
            // --- MODIFIKASI DIMULAI: Ubah alias kembali menjadi 'tanggal_bayar' ---
            ->select('tagihan_spp_siswa.nis, siswa.nama_siswa, siswa.kelas, tagihan_spp.nama_tagihan, tagihan_spp.nominal as jumlah_tagihan, tagihan_spp_siswa.jumlah_bayar, tagihan_spp_siswa.updated_at as tanggal_bayar, tagihan_spp_siswa.status, tagihan_spp_siswa.catatan_walimurid as catatan')
            // --- MODIFIKASI SELESAI ---
            ->join('siswa', 'siswa.nis = tagihan_spp_siswa.nis', 'left')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id', 'left')
            ->where('tagihan_spp_siswa.updated_at >=', $startDate)
            ->where('tagihan_spp_siswa.updated_at <=', $endDate);

        if (!empty($filter['status_spp'])) {
            $query->where('tagihan_spp_siswa.status', $filter['status_spp']);
        }        
        if (!empty($filter['search_spp'])) {
            $query->groupStart()
                  ->like('tagihan_spp_siswa.nis', $filter['search_spp'])
                  ->orLike('siswa.nama_siswa', $filter['search_spp'])
                  ->orLike('siswa.kelas', $filter['search_spp'])
                  ->orLike('tagihan_spp.nama_tagihan', $filter['search_spp'])
                  ->orWhere('tagihan_spp.nominal', $filter['search_spp'])
                  ->orWhere('tagihan_spp_siswa.jumlah_bayar', $filter['search_spp'])
                  ->orWhere('tagihan_spp_siswa.status', $filter['search_spp'])
                  ->orLike('tagihan_spp_siswa.catatan_walimurid', $filter['search_spp'])
                  ->groupEnd();
        }
        
        return $query;
    }

    private function buildUangSakuQuery(array $filter)
    {
        if ($filter['periode_tipe_us'] === 'tahunan') {
            $tahun = $filter['periode_tahun_us'];
            $startDate = "$tahun-01-01 00:00:00";
            $endDate = "$tahun-12-31 23:59:59";
        } else {
            $bulan = $filter['periode_bulan_us'];
            $startDate = "$bulan-01 00:00:00";
            $endDate = date('Y-m-t', strtotime($startDate)) . " 23:59:59";
        }

        $query = $this->transaksiUangSakuModel
            ->select('transaksi_uang_saku.nis, siswa.nama_siswa, siswa.kelas, transaksi_uang_saku.nominal, transaksi_uang_saku.tanggal, transaksi_uang_saku.tipe_transaksi as status, transaksi_uang_saku.catatan')
            ->join('siswa', 'siswa.nis = transaksi_uang_saku.nis', 'left')
            ->where('transaksi_uang_saku.tanggal >=', $startDate)
            ->where('transaksi_uang_saku.tanggal <=', $endDate);

        if (!empty($filter['status_us'])) {
            $query->where('transaksi_uang_saku.tipe_transaksi', $filter['status_us']);
        }

        if (!empty($filter['search_us'])) {
            $query->groupStart()
                  ->like('transaksi_uang_saku.nis', $filter['search_us'])
                  ->orLike('siswa.nama_siswa', $filter['search_us'])
                  ->orLike('siswa.kelas', $filter['search_us'])
                  ->orWhere('transaksi_uang_saku.nominal', $filter['search_us'])
                  ->orLike('transaksi_uang_saku.catatan', $filter['search_us'])
                  ->groupEnd();
        }

        return $query;
    }
}