<?php
namespace App\Controllers\Admin;
// C:\xampp\htdocs\payment-gateway\app\Controllers\Admin\RincianSppSiswa.php
use App\Controllers\BaseController;
use App\Models\TagihanSppSiswaModel;
use App\Models\SiswaModel;
use App\Models\PembayaranSppModel;

class RincianSppSiswa extends BaseController
{
    protected $tagihanSppSiswaModel;
    protected $siswaModel;
    protected $pembayaranSppModel;

    public function __construct()
    {
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
        $this->siswaModel = model(SiswaModel::class);
        $this->pembayaranSppModel = model(PembayaranSppModel::class);
    }

    public function index()
    {
        // MODIFIKASI: Nama filter dibuat unik untuk setiap tab
        $filter = [
            'tab'                 => $this->request->getGet('tab') ?? 'aktif',
            'periode_aktif'       => $this->request->getGet('periode_aktif') ?? '',
            'kelas_aktif'         => $this->request->getGet('kelas_aktif') ?? '',
            'status_bayar_aktif'  => $this->request->getGet('status_bayar_aktif') ?? '',
            'search_aktif'        => $this->request->getGet('search_aktif') ?? '',
            'periode_riwayat'     => $this->request->getGet('periode_riwayat') ?? '',
            'kelas_riwayat'       => $this->request->getGet('kelas_riwayat') ?? '',
            'status_bayar_riwayat'=> $this->request->getGet('status_bayar_riwayat') ?? '',
            'search_riwayat'      => $this->request->getGet('search_riwayat') ?? '',
        ];

        // --- Query untuk TAB 1: TANGGUNGAN AKTIF ---
        $tanggunganAktifQuery = $this->tagihanSppSiswaModel->builder()
            ->select('tagihan_spp_siswa.*, siswa.nama_siswa as nama, siswa.kelas, siswa.telepon_orangtua, tagihan_spp.nama_tagihan, tagihan_spp.jatuh_tempo, tagihan_spp.nominal, tagihan_spp.id as master_tagihan_id')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->join('siswa', 'siswa.nis = tagihan_spp_siswa.nis')
            ->where('tagihan_spp.status_tagihan', 'Aktif')
            ->where('tagihan_spp_siswa.status', 'Belum Lunas');
        
        // Terapkan filter ke query Tanggungan Aktif
        if (!empty($filter['periode_aktif'])) { $tanggunganAktifQuery->where("DATE_FORMAT(tagihan_spp.jatuh_tempo, '%Y-%m') =", $filter['periode_aktif']); }
        if (!empty($filter['kelas_aktif'])) { $tanggunganAktifQuery->where('siswa.kelas', $filter['kelas_aktif']); }
        if (!empty($filter['search_aktif'])) { 
            $tanggunganAktifQuery->groupStart()
                ->like('siswa.nis', $filter['search_aktif'])
                ->orLike('siswa.nama_siswa', $filter['search_aktif'])
                ->orLike('tagihan_spp.nama_tagihan', $filter['search_aktif'])
                ->groupEnd(); 
        }
        if (!empty($filter['status_bayar_aktif'])) {
            if($filter['status_bayar_aktif'] === 'Dicicil') {
                $tanggunganAktifQuery->where('tagihan_spp_siswa.jumlah_bayar >', 0);
            } elseif ($filter['status_bayar_aktif'] === 'Belum Lunas') {
                $tanggunganAktifQuery->where('tagihan_spp_siswa.jumlah_bayar', 0);
            }
        }
        $tanggunganAktifList = $tanggunganAktifQuery->orderBy('tagihan_spp.jatuh_tempo', 'ASC')->get()->getResultArray();

        // --- Query untuk TAB 2: RIWAYAT & ARSIP ---
        $riwayatQuery = $this->tagihanSppSiswaModel->builder()
            ->select('tagihan_spp_siswa.*, siswa.nama_siswa as nama, siswa.kelas, siswa.telepon_orangtua, tagihan_spp.nama_tagihan, tagihan_spp.jatuh_tempo, tagihan_spp.nominal, tagihan_spp.id as master_tagihan_id, tagihan_spp.status_tagihan')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->join('siswa', 'siswa.nis = tagihan_spp_siswa.nis')
            ->groupStart()
                ->where('tagihan_spp_siswa.status', 'Lunas')
                ->orWhere('tagihan_spp.status_tagihan', 'Diarsipkan')
            ->groupEnd();

        // Terapkan filter ke query Riwayat & Arsip
        if (!empty($filter['periode_riwayat'])) { $riwayatQuery->where("DATE_FORMAT(tagihan_spp.jatuh_tempo, '%Y-%m') =", $filter['periode_riwayat']); }
        if (!empty($filter['kelas_riwayat'])) { $riwayatQuery->where('siswa.kelas', $filter['kelas_riwayat']); }
        if (!empty($filter['search_riwayat'])) { 
            $riwayatQuery->groupStart()
                ->like('siswa.nis', $filter['search_riwayat'])
                ->orLike('siswa.nama_siswa', $filter['search_riwayat'])
                ->orLike('tagihan_spp.nama_tagihan', $filter['search_riwayat'])
                ->groupEnd(); 
        }
        if (!empty($filter['status_bayar_riwayat'])) {
            if($filter['status_bayar_riwayat'] === 'Lunas') {
                $riwayatQuery->where('tagihan_spp_siswa.status', 'Lunas');
            } elseif ($filter['status_bayar_riwayat'] === 'Diarsipkan') {
                $riwayatQuery->where('tagihan_spp.status_tagihan', 'Diarsipkan')->where('tagihan_spp_siswa.status !=', 'Lunas');
            }
        }
        $riwayatList = $riwayatQuery->orderBy('tagihan_spp_siswa.updated_at', 'DESC')->get()->getResultArray();

        return view('admin/rincian_spp_siswa', [
            'tanggunganAktifList' => $tanggunganAktifList,
            'riwayatList'         => $riwayatList,
            'filter'              => $filter,
        ]);
    }

        public function cetak_invoice($tagihan_spp_siswa_id)
    {
        // ...
        $tagihan = $this->tagihanSppSiswaModel
            ->select('tagihan_spp_siswa.*, siswa.nama_siswa, siswa.kelas, tagihan_spp.nama_tagihan, tagihan_spp.nominal as total_tagihan, tagihan_spp.status_tagihan') // <-- TAMBAHKAN DI SINI
            ->join('siswa', 'siswa.nis = tagihan_spp_siswa.nis')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.id', $tagihan_spp_siswa_id)
            ->first();

        if (!$tagihan) {
            return redirect()->back()->with('error', 'Tidak ditemukan riwayat pembayaran untuk tagihan ini.');
        }
        
        // Kirim juga data siswa ke view cetak invoice
        $siswa = $this->siswaModel->find($tagihan['nis']);

        $cicilan = $this->pembayaranSppModel
            ->select("pembayaran_spp.*, CASE WHEN pembayaran_spp.midtrans_order_id LIKE 'MANUAL-%' THEN 'Pembayaran Manual' ELSE midtrans_log.payment_type END as payment_type")
            ->join('midtrans_log', 'midtrans_log.order_id = pembayaran_spp.midtrans_order_id', 'left')
            ->where('pembayaran_spp.tagihan_spp_id', $tagihan['tagihan_id'])
            ->where('pembayaran_spp.nis', $tagihan['nis'])
            ->where('pembayaran_spp.status', 'Sukses')
            ->groupBy('pembayaran_spp.id')
            ->orderBy('pembayaran_spp.tanggal_bayar', 'ASC')
            ->findAll();

        return view('admin/cetak_invoice', ['tagihan' => $tagihan, 'cicilan' => $cicilan, 'siswa' => $siswa]);
    }
}