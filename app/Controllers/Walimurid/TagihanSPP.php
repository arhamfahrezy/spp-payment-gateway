<?php
namespace App\Controllers\WaliMurid;
// C:\xampp\htdocs\payment-gateway\app\Controllers\Walimurid\TagihanSPP.php
use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\TagihanSppSiswaModel;

class TagihanSPP extends BaseController
{
    protected $siswaModel;
    protected $tagihanSppSiswaModel;

    public function __construct()
    {
        $this->siswaModel = model(SiswaModel::class);
        $this->tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);
    }

    public function index()
    {
        $userId = session()->get('user')['id'];
        $siswa = $this->siswaModel->where('user_id', $userId)->first();

        if (!$siswa) {
            return view('walimurid/tagihan_spp', [
                'daftarTagihan' => [],
                'namaSiswa' => 'Siswa tidak ditemukan',
            ]);
        }

        $nis = $siswa['nis'];
        $namaSiswa = $siswa['nama_siswa'];

        $daftarTagihan = $this->tagihanSppSiswaModel
            ->select('
                tagihan_spp.id as id_tagihan_untuk_form, 
                tagihan_spp.nama_tagihan, 
                tagihan_spp.jatuh_tempo, 
                tagihan_spp.nominal, 
                tagihan_spp_siswa.status, 
                tagihan_spp_siswa.id as tagihan_spp_siswa_id,
                tagihan_spp_siswa.jumlah_bayar,
                pembayaran.status as status_pembayaran_pending
            ')
            ->join('tagihan_spp', 'tagihan_spp.id = tagihan_spp_siswa.tagihan_id')
            ->join('(SELECT tagihan_spp_id, status FROM pembayaran_spp WHERE status = "Pending") AS pembayaran', 'pembayaran.tagihan_spp_id = tagihan_spp.id', 'left')
            ->where('tagihan_spp_siswa.nis', $nis)
            ->where('tagihan_spp_siswa.status', 'Belum Lunas')
            ->where('tagihan_spp.status_tagihan', 'Aktif') 
            ->orderBy('tagihan_spp.jatuh_tempo', 'ASC')
            ->findAll();

        return view('walimurid/tagihan_spp', [
            'daftarTagihan' => $daftarTagihan,
            'namaSiswa' => $namaSiswa,
        ]);
    }
}