<?php
namespace App\Controllers\WaliMurid;
// C:\xampp\htdocs\payment-gateway\app\Controllers\Walimurid\UangSaku.php
use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\UangSakuModel;
use App\Models\TransaksiUangSakuModel;
use App\Models\PembayaranUangSakuModel;

class UangSaku extends BaseController
{
    protected $siswaModel;
    protected $uangSakuModel;
    protected $transaksiUangSakuModel;
    protected $pembayaranUangSakuModel;

    public function __construct()
    {
        $this->siswaModel = model(SiswaModel::class);
        $this->uangSakuModel = model(UangSakuModel::class);
        $this->transaksiUangSakuModel = model(TransaksiUangSakuModel::class);
        $this->pembayaranUangSakuModel = model(PembayaranUangSakuModel::class);
        
    }

    public function index()
    {

        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();

        if (!$siswa) {
            return view('walimurid/uang_saku', [
                'namaSiswa' => 'Data Siswa Tidak Ditemukan', 'saldoUangSaku' => 0,
                'uangSakuMasukTotal' => 0, 'uangSakuDiambilTotal' => 0,
                'riwayatTransaksi' => [], 'filter' => []
            ]);
        }

        $nis = $siswa['nis'];
        $namaSiswa = $siswa['nama_siswa'];
        $filter = ['periode' => $this->request->getGet('periode') ?? '', 'tipe' => $this->request->getGet('tipe') ?? '', 'search' => $this->request->getGet('search') ?? ''];

        $transaksiFinalQuery = $this->transaksiUangSakuModel->builder();
        $transaksiFinalQuery->where('nis', $nis);

        $transaksiUpayaQuery = $this->pembayaranUangSakuModel->builder();
        $transaksiUpayaQuery->where('nis', $nis)->whereIn('status', ['Pending', 'Gagal']);

        // Menjadi seperti ini
        $queryForCards = $this->transaksiUangSakuModel->builder();
        $queryForCards->where('nis', $nis);

        if (!empty($filter['periode'])) {
            $periode = $filter['periode'];
            $transaksiFinalQuery->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode);
            $transaksiUpayaQuery->where("DATE_FORMAT(created_at, '%Y-%m') =", $periode);
            $queryForCards->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode);
        }
        
        if (!empty($filter['tipe'])) {
            if (in_array($filter['tipe'], ['Masuk', 'Keluar'])) {
                $transaksiFinalQuery->where('tipe_transaksi', $filter['tipe']);
                $transaksiUpayaQuery->where('1=0');
            } elseif (in_array($filter['tipe'], ['Pending', 'Gagal'])) {
                $transaksiUpayaQuery->where('status', $filter['tipe']);
                $transaksiFinalQuery->where('1=0');
            }
        }
        
        if (!empty($filter['search'])) {
            $searchTerm = $filter['search'];
            $transaksiFinalQuery->groupStart()->like('catatan', $searchTerm)->orWhere('nominal', $searchTerm)->groupEnd();
            $transaksiUpayaQuery->groupStart()->like('catatan_walimurid', $searchTerm)->orWhere('jumlah_bayar', $searchTerm)->groupEnd();
        }

        $riwayatFinal = $transaksiFinalQuery->select("id, tanggal, catatan, tipe_transaksi as status, nominal, '' as order_id")->get()->getResultArray();
        $riwayatUpaya = $transaksiUpayaQuery->select("id, created_at as tanggal, catatan_walimurid as catatan, status, jumlah_bayar as nominal, midtrans_order_id as order_id")->get()->getResultArray();

        $riwayatTransaksi = array_merge($riwayatFinal, $riwayatUpaya);
        usort($riwayatTransaksi, function ($a, $b) { return strtotime($b['tanggal']) - strtotime($a['tanggal']); });

        // Hitung total untuk kartu statistik menggunakan query khususnya
        // ... DENGAN BLOK YANG BARU INI

        // Hitung total untuk kartu statistik (WORKAROUND UNTUK CI4 VERSI LAMA)
        // Kita sisipkan komentar unik pada nama alias untuk mengakali cache.
        $uangSakuMasukTotal = (clone $queryForCards)
            ->where('tipe_transaksi', 'Masuk')
            ->selectSum('nominal', 'total_' . uniqid()) // Menambahkan ID unik ke alias
            ->get()->getRow('total_' . uniqid()) ?? 0;

        // Karena kita tidak bisa mendapatkan nama alias yang sama persis, kita query ulang. Ini tidak efisien tapi pasti berhasil.
        $uangSakuMasukData = $this->transaksiUangSakuModel->builder()
            ->where('nis', $nis)
            ->where('tipe_transaksi', 'Masuk')
            ->selectSum('nominal', 'total')->get()->getRow();
        $uangSakuMasukTotal = $uangSakuMasukData->total ?? 0;
            
        $uangSakuDiambilData = $this->transaksiUangSakuModel->builder()
            ->where('nis', $nis)
            ->where('tipe_transaksi', 'Keluar')
            ->selectSum('nominal', 'total')->get()->getRow();
        $uangSakuDiambilTotal = $uangSakuDiambilData->total ?? 0;

        // Jika ada filter periode, terapkan juga di sini.
        if (!empty($filter['periode'])) {
            $periode = $filter['periode'];
            
            $uangSakuMasukData = $this->transaksiUangSakuModel->builder()
                ->where('nis', $nis)
                ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode)
                ->where('tipe_transaksi', 'Masuk')
                ->selectSum('nominal', 'total')->get()->getRow();
            $uangSakuMasukTotal = $uangSakuMasukData->total ?? 0;
                
            $uangSakuDiambilData = $this->transaksiUangSakuModel->builder()
                ->where('nis', $nis)
                ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode)
                ->where('tipe_transaksi', 'Keluar')
                ->selectSum('nominal', 'total')->get()->getRow();
            $uangSakuDiambilTotal = $uangSakuDiambilData->total ?? 0;
        }

        $saldoUangSaku = $this->uangSakuModel->find($nis)['saldo'] ?? 0;

        return view('walimurid/uang_saku', [
            'namaSiswa' => $namaSiswa, 'saldoUangSaku' => $saldoUangSaku,
            'uangSakuMasukTotal' => $uangSakuMasukTotal, 'uangSakuDiambilTotal' => $uangSakuDiambilTotal,
            'riwayatTransaksi' => $riwayatTransaksi, 'filter' => $filter,
        ]);
    }

    public function cetakBukti($transaksi_id)
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) {
            return redirect()->to('walimurid/dashboard')->with('error', 'Siswa tidak ditemukan.');
        }
        $transaksi = $this->transaksiUangSakuModel->find($transaksi_id);
        if (!$transaksi || $transaksi['nis'] !== $siswa['nis']) {
            return redirect()->to('walimurid/uang_saku')->with('error', 'Transaksi tidak ditemukan atau bukan milik Anda.');
        }
        $data = ['transaksi' => $transaksi, 'siswa' => $siswa,];
        return view('walimurid/bukti_transaksi_uang_saku', $data);
    }
}