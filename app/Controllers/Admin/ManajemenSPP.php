<?php
namespace App\Controllers\Admin;
// C:\xampp\htdocs\payment-gateway\app\Controllers\Admin\ManajemenSPP.php
use App\Controllers\BaseController;
use App\Models\TagihanModel;
use App\Models\TagihanSppSiswaModel;

class ManajemenSPP extends BaseController
{
    protected $tagihanModel;
    protected $tagihanSppSiswaModel;

    public function __construct()
    {
        $this->tagihanModel = model(TagihanModel::class);
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
    }

    public function index()
    {
        $filter = [
            'tab'           => $this->request->getGet('tab') ?? 'aktif',
            'periode_aktif' => $this->request->getGet('periode_aktif') ?? '',
            'kelas_aktif'   => $this->request->getGet('kelas_aktif') ?? '',
            'search_aktif'  => $this->request->getGet('search_aktif') ?? '',
            // Filter untuk arsip bisa ditambahkan di sini nanti jika perlu
        ];

        // Ambil data untuk Tab "Tagihan Aktif"
        $sppAktifQuery = $this->tagihanModel
            ->select("tagihan_spp.*, (SELECT COUNT(*) FROM tagihan_spp_siswa WHERE tagihan_spp_siswa.tagihan_id = tagihan_spp.id) as total_siswa, (SELECT COUNT(*) FROM tagihan_spp_siswa WHERE tagihan_spp_siswa.tagihan_id = tagihan_spp.id AND tagihan_spp_siswa.status = 'Lunas') as lunas_siswa")
            ->where('status_tagihan', 'Aktif'); // HANYA AMBIL YANG AKTIF
            
        if (!empty($filter['periode_aktif'])) { $sppAktifQuery->where("DATE_FORMAT(jatuh_tempo, '%Y-%m') =", $filter['periode_aktif']); }
        if (!empty($filter['kelas_aktif'])) { $sppAktifQuery->where('kelas', $filter['kelas_aktif']); }
        if (!empty($filter['search_aktif'])) { $sppAktifQuery->groupStart()->like('nama_tagihan', $filter['search_aktif'])->orWhere('nominal', $filter['search_aktif'])->groupEnd(); }
        $sppAktifList = $sppAktifQuery->orderBy('jatuh_tempo', 'DESC')->findAll();

        // Ambil data untuk Tab "Arsip Tagihan"
        $sppArsipList = $this->tagihanModel
            ->where('status_tagihan', 'Diarsipkan') // HANYA AMBIL YANG DIARSIPKAN
            ->orderBy('jatuh_tempo', 'DESC')
            ->findAll();
        
        // Data untuk statistik card
        $sppAktifCount = $this->tagihanModel->where('status_tagihan', 'Aktif')->countAllResults();

        $sppLunasCount = $this->tagihanSppSiswaModel
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.status', 'Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // Hanya hitung dari tagihan aktif
            ->countAllResults();

        $sppTanggunganCount = $this->tagihanSppSiswaModel
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // Hanya hitung dari tagihan aktif
            ->countAllResults();

        return view('admin/manajemen_spp', [
            'sppAktif' => $sppAktifCount,
            'sppLunas' => $sppLunasCount,
            'sppTanggungan' => $sppTanggunganCount,
            'sppAktifList' => $sppAktifList,
            'sppArsipList' => $sppArsipList, // Kirim data arsip ke view
            'filter' => $filter,
        ]);
    }
    
    // FUNGSI BARU UNTUK MENGARSIPKAN
    public function archive($id)
    {
        $this->tagihanModel->update($id, ['status_tagihan' => 'Diarsipkan']);
        return redirect()->to('admin/manajemen_spp')->with('success', 'Tagihan berhasil diarsipkan.');
    }

    // FUNGSI BARU UNTUK MENGAKTIFKAN KEMBALI
    public function reactivate($id)
    {
        $this->tagihanModel->update($id, ['status_tagihan' => 'Aktif']);
        return redirect()->to('admin/manajemen_spp?tab=arsip')->with('success', 'Tagihan berhasil diaktifkan kembali.');
    }
    
    // public function hapus_tagihan($id)
    // {
    //     // Fungsi ini tidak berubah
    //     $tagihan = $this->tagihanModel->find($id);
    //     if ($tagihan) {
    //         $this->tagihanModel->delete($id);
    //         return redirect()->to('admin/manajemen_spp')->with('success', 'Master tagihan dan semua tagihan siswa terkait berhasil dihapus.');
    //     } else {
    //         return redirect()->to('admin/manajemen_spp')->with('error', 'Data master tagihan tidak ditemukan.');
    //     }
    // }

    // public function cetak_invoice($tagihan_spp_siswa_id)
    // {
    //     // Fungsi ini tidak berubah
    //     $tagihan = $this->tagihanSppSiswaModel
    //         ->select('tagihan_spp_siswa.*, siswa.nama_siswa, siswa.kelas, tagihan_spp.nama_tagihan, tagihan_spp.nominal as total_tagihan')
    //         ->join('siswa', 'siswa.nis = tagihan_spp_siswa.nis')
    //         ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
    //         ->where('tagihan_spp_siswa.id', $tagihan_spp_siswa_id)
    //         ->first();

    //     if (!$tagihan) {
    //         return redirect()->back()->with('error', 'Tidak ditemukan riwayat pembayaran untuk tagihan ini.');
    //     }

    //     $cicilan = $this->pembayaranSppModel
    //         ->select("pembayaran_spp.*, CASE WHEN pembayaran_spp.midtrans_order_id LIKE 'MANUAL-%' THEN 'Pembayaran Manual' ELSE midtrans_log.payment_type END as payment_type")
    //         ->join('midtrans_log', 'midtrans_log.order_id = pembayaran_spp.midtrans_order_id', 'left')
    //         ->where('pembayaran_spp.tagihan_spp_id', $tagihan['tagihan_id'])
    //         ->where('pembayaran_spp.nis', $tagihan['nis'])
    //         ->where('pembayaran_spp.status', 'Sukses')
    //         ->groupBy('pembayaran_spp.id')
    //         ->orderBy('pembayaran_spp.tanggal_bayar', 'ASC')
    //         ->findAll();

    //     return view('admin/cetak_invoice', ['tagihan' => $tagihan, 'cicilan' => $cicilan]);
    // }
}