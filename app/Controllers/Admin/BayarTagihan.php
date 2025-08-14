<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\TagihanSppSiswaModel;
use App\Models\TagihanModel;
use App\Models\PembayaranSppModel;

class BayarTagihan extends BaseController
{
    protected $siswaModel;
    protected $tagihanSppSiswaModel;
    protected $tagihanModel;
    protected $pembayaranSppModel;

    public function __construct()
    {
        $this->siswaModel = model(SiswaModel::class);
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
        $this->tagihanModel = model(TagihanModel::class);
        $this->pembayaranSppModel = model(PembayaranSppModel::class);
    }

    public function index()
    {
        $data = [
            'siswaList' => $this->siswaModel->select('nis, nama_siswa, kelas')->orderBy('nama_siswa', 'ASC')->findAll(),
            'preselectedNis' => $this->request->getGet('nis'),
            'preselectedTagihanId' => $this->request->getGet('tagihan_id'),
        ];
        return view('admin/bayar_tagihan', $data);
    }

    public function get_tagihan_siswa($nis)
    {
        $tagihanList = $this->tagihanSppSiswaModel
            ->select('tagihan_spp.id, tagihan_spp.nama_tagihan, tagihan_spp.jatuh_tempo, tagihan_spp.nominal, tagihan_spp_siswa.jumlah_bayar, tagihan_spp_siswa.status')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->where('tagihan_spp_siswa.nis', $nis)
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') // <-- MODIFIKASI: Hanya ambil tagihan aktif
            ->orderBy('tagihan_spp.jatuh_tempo', 'ASC')
            ->findAll();

        $response = array_map(function($tagihan) {
            $tagihan['sisa_tagihan'] = (int)$tagihan['nominal'] - (int)$tagihan['jumlah_bayar'];
            return $tagihan;
        }, $tagihanList);

        return $this->response->setJSON($response);
    }

    // Memproses pembayaran yang diinput oleh admin
    public function proses_pembayaran()
    {
        $post = $this->request->getPost();
        $rules = [
            'nis_siswa' => 'required',
            'tagihan_id' => 'required',
            'jumlah_bayar' => 'required',
            'tanggal_bayar' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom wajib diisi dan harus valid.');
        }

        $nis = $post['nis_siswa'];
        $tagihanId = $post['tagihan_id'];
        $jumlahBayarBaru = (int)str_replace('.', '', $post['jumlah_bayar']);
        $tanggalBayar = $post['tanggal_bayar'];
        $catatanAdmin = $post['catatan'] ?: 'Pembayaran tunai';

        $tagihanSiswa = $this->tagihanSppSiswaModel->where('nis', $nis)->where('tagihan_id', $tagihanId)->first();
        $tagihanInduk = $this->tagihanModel->find($tagihanId);

        if (!$tagihanSiswa || !$tagihanInduk) {
            return redirect()->back()->withInput()->with('error', 'Data tagihan siswa tidak ditemukan.');
        }
        
        // <-- MODIFIKASI: Tambahkan validasi untuk tagihan yang diarsipkan
        if ($tagihanInduk['status_tagihan'] === 'Diarsipkan') {
            return redirect()->back()->withInput()->with('error', 'Tidak dapat memproses pembayaran. Tagihan ini sudah diarsipkan.');
        }

        $sisaTagihan = (int)$tagihanInduk['nominal'] - (int)$tagihanSiswa['jumlah_bayar'];
        if ($jumlahBayarBaru > $sisaTagihan) {
            return redirect()->back()->withInput()->with('error', 'Jumlah bayar tidak boleh melebihi sisa tagihan.');
        }
        if ($jumlahBayarBaru <= 0) {
            return redirect()->back()->withInput()->with('error', 'Jumlah bayar harus lebih dari nol.');
        }


        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Catat di tabel log pembayaran (pembayaran_spp)
        $this->pembayaranSppModel->insert([
            'nis' => $nis,
            'tagihan_spp_id' => $tagihanId,
            'jumlah_bayar' => $jumlahBayarBaru,
            'tanggal_bayar' => $tanggalBayar . ' ' . date('H:i:s'),
            'status' => 'Sukses',
            'midtrans_order_id' => 'MANUAL-' . $nis . '-' . time(), // Order ID unik untuk manual
            'catatan' => $catatanAdmin
        ]);

        // 2. Update tabel ringkasan tagihan (tagihan_spp_siswa)
        $totalSudahDibayar = (int)$tagihanSiswa['jumlah_bayar'] + $jumlahBayarBaru;
        $statusTagihanBaru = ($totalSudahDibayar >= (int)$tagihanInduk['nominal']) ? 'Lunas' : 'Belum Lunas';
        
        $catatanUpdate = $tagihanSiswa['catatan_walimurid'];
        if ($catatanAdmin) {
            $catatanUpdate = ($catatanUpdate ? $catatanUpdate . "\n" : "") . $catatanAdmin;
        }

        $this->tagihanSppSiswaModel->update($tagihanSiswa['id'], [
            'jumlah_bayar' => $totalSudahDibayar,
            'status' => $statusTagihanBaru,
            'tanggal_bayar' => $tanggalBayar,
            'catatan_walimurid' => $catatanUpdate
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan database, pembayaran gagal dicatat.');
        }

        return redirect()->to('admin/rincian_spp_siswa?search_siswa='.$nis)->with('success', 'Pembayaran manual berhasil dicatat.');
    }
}