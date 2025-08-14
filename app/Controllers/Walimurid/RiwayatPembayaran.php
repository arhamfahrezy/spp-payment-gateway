<?php
namespace App\Controllers\WaliMurid;
// C:\xampp\htdocs\payment-gateway\app\Controllers\Walimurid\RiwayatPembayaran.php
use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\PembayaranSppModel;
use App\Models\TagihanSppSiswaModel;

class RiwayatPembayaran extends BaseController
{
    protected $siswaModel;
    protected $pembayaranSppModel;
    protected $tagihanSppSiswaModel;

    public function __construct()
    {
        $this->siswaModel = model(SiswaModel::class);
        $this->pembayaranSppModel = model(PembayaranSppModel::class);
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
    }

    public function index()
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        
        $sppFilter = [
            'periode_tipe' => $this->request->getGet('periode_tipe_spp') ?? 'bulanan',
            'periode_bulan' => $this->request->getGet('periode_bulan_spp') ?? date('Y-m'),
            'periode_tahun' => $this->request->getGet('periode_tahun_spp') ?? date('Y'),
            'status' => $this->request->getGet('status_spp') ?? '',
        ];
        
        if (!$siswa) {
            return view('walimurid/riwayat_pembayaran', [
                'namaSiswa' => 'Siswa tidak ditemukan', 
                'riwayatSPP' => [],
                'sppFilter' => $sppFilter,
            ]);
        }

        $nis = $siswa['nis'];
        $namaSiswa = $siswa['nama_siswa'];
        
        $riwayatSPP = $this->getRiwayatSpp($nis, $sppFilter);

        return view('walimurid/riwayat_pembayaran', [
            'namaSiswa' => $namaSiswa, 
            'riwayatSPP' => $riwayatSPP,
            'sppFilter' => $sppFilter,
        ]);
    }

    private function getRiwayatSpp($nis, $filter)
    {
        if ($filter['periode_tipe'] === 'tahunan') {
            $startDate = $filter['periode_tahun'] . '-01-01 00:00:00';
            $endDate = $filter['periode_tahun'] . '-12-31 23:59:59';
        } else {
            $startDate = $filter['periode_bulan'] . '-01 00:00:00';
            $endDate = date('Y-m-t', strtotime($startDate)) . ' 23:59:59';
        }

        // 1. Query Ringkasan (Lunas & Dicicil)
        $queryRingkasan = $this->tagihanSppSiswaModel
            ->select("
                tagihan_spp_siswa.id as transaksi_id, 
                tagihan_spp_siswa.updated_at as tanggal, 
                tagihan_spp_siswa.jumlah_bayar as jumlah, 
                CASE 
                    WHEN tagihan_spp_siswa.status = 'Lunas' THEN 'Lunas'
                    ELSE 'Dicicil'
                END as status,
                tagihan_spp.nama_tagihan,
                tagihan_spp.nominal as total_tagihan,
                tagihan_spp_siswa.tagihan_id as id_tagihan_untuk_form,
                tagihan_spp_siswa.id as tagihan_spp_siswa_id,
                tagihan_spp.status_tagihan as status_tagihan 
            ")
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id', 'left')
            ->where('tagihan_spp_siswa.nis', $nis)
            ->where('tagihan_spp_siswa.updated_at >=', $startDate)
            ->where('tagihan_spp_siswa.updated_at <=', $endDate)
            ->where('tagihan_spp_siswa.jumlah_bayar >', 0);

        // 2. Query Log (Pending & Gagal)
        $queryLog = $this->pembayaranSppModel
            ->select("
                pembayaran_spp.id as transaksi_id, 
                pembayaran_spp.updated_at as tanggal, 
                pembayaran_spp.jumlah_bayar as jumlah, 
                pembayaran_spp.status, 
                tagihan_spp.nama_tagihan,
                tagihan_spp.nominal as total_tagihan,
                pembayaran_spp.tagihan_spp_id as id_tagihan_untuk_form,
                null as tagihan_spp_siswa_id,
                tagihan_spp.status_tagihan as status_tagihan
            ")
            ->join('tagihan_spp', 'tagihan_spp.id = pembayaran_spp.tagihan_spp_id', 'left')
            ->where('pembayaran_spp.nis', $nis)
            ->where('pembayaran_spp.created_at >=', $startDate)
            ->where('pembayaran_spp.created_at <=', $endDate)
            ->whereIn('pembayaran_spp.status', ['Pending', 'Gagal']);
        
        // Filter status (tidak berubah)
        $statusFilter = $filter['status'];
        if (!empty($statusFilter)) {
            if ($statusFilter === 'Lunas') {
                $queryRingkasan->where('tagihan_spp_siswa.status', 'Lunas');
                $queryLog->where('1=0');
            } elseif ($statusFilter === 'Dicicil') {
                $queryRingkasan->where('tagihan_spp_siswa.status', 'Belum Lunas');
                $queryLog->where('1=0');
            } else {
                $queryLog->where('pembayaran_spp.status', $statusFilter);
                $queryRingkasan->where('1=0');
            }
        }

        $dataRingkasan = $queryRingkasan->findAll();
        $dataLog = $queryLog->findAll();
        
        $hasilGabungan = array_merge($dataRingkasan, $dataLog);
        
        usort($hasilGabungan, function ($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        return $hasilGabungan;
    }

    public function cetakBuktiSpp($tagihan_spp_siswa_id)
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) return redirect()->to('walimurid/dashboard')->with('error', 'Siswa tidak ditemukan.');

        $nis = $siswa['nis'];

        // MODIFIKASI: Logika diubah agar bisa mencetak bukti untuk 'Dicicil (Diarsipkan)' juga.
        $tagihan = $this->tagihanSppSiswaModel
            ->select('tagihan_spp_siswa.*, tagihan_spp.nama_tagihan, tagihan_spp.nominal, tagihan_spp.status_tagihan') // <-- TAMBAHKAN DI SINI
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.id', $tagihan_spp_siswa_id)
            ->first();

        if (!$tagihan || $tagihan['nis'] !== $nis) {
            return redirect()->to('walimurid/riwayat_pembayaran')->with('error', 'Tagihan tidak ditemukan atau bukan milik Anda.');
        }

        // Ambil cicilan (tidak berubah)
        $cicilan = $this->pembayaranSppModel
            ->select("pembayaran_spp.*, CASE WHEN pembayaran_spp.midtrans_order_id LIKE 'MANUAL-%' THEN 'Pembayaran Manual' ELSE midtrans_log.payment_type END as payment_type")
            ->join('midtrans_log', 'midtrans_log.order_id = pembayaran_spp.midtrans_order_id', 'left')
            ->where('pembayaran_spp.tagihan_spp_id', $tagihan['tagihan_id'])
            ->where('pembayaran_spp.nis', $nis)
            ->where('pembayaran_spp.status', 'Sukses')
            ->groupBy('pembayaran_spp.id')
            ->orderBy('pembayaran_spp.tanggal_bayar', 'ASC')
            ->findAll();

        return view('walimurid/bukti_pembayaran_spp', ['tagihan' => $tagihan, 'cicilan' => $cicilan, 'siswa' => $siswa,]);
    }
}