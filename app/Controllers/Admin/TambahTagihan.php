<?php
// C:\xampp\htdocs\payment-gateway\app\Controllers\Admin\TambahTagihan.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TagihanModel;
use App\Models\SiswaModel;
use App\Models\TagihanSppSiswaModel;

class TambahTagihan extends BaseController
{
    public function index()
    {
        return view('admin/tambah_tagihan');
    }

    public function edit($id)
    {
        $tagihanModel = new TagihanModel();
        $tagihan = $tagihanModel->find($id);

        if (!$tagihan) {
            return redirect()->to('admin/manajemen_spp')->with('error', 'Data tagihan tidak ditemukan.');
        }

        $data = [
            'title'   => 'Edit Tagihan SPP',
            'tagihan' => $tagihan,
        ];

        return view('admin/tambah_tagihan', $data);
    }
 
    public function simpan()
    {
        $tagihanModel = new TagihanModel();
        $siswaModel = new SiswaModel();
        $tagihanSppSiswaModel = new TagihanSppSiswaModel();
        
        $nominal = str_replace('.', '', $this->request->getPost('nominal'));
        $data = [
            'nama_tagihan' => $this->request->getPost('nama_tagihan'),
            'kelas'        => $this->request->getPost('kelas'),
            'jatuh_tempo'  => $this->request->getPost('jatuh_tempo'),
            'nominal'      => $nominal,
        ];
        
        // MODIFIKASI: Aturan validasi dengan pesan kustom
        $rules = [
            'nama_tagihan' => [
                'rules' => 'required|is_unique[tagihan_spp.nama_tagihan]',
                'errors' => [
                    'required' => 'Nama tagihan wajib diisi.',
                    'is_unique' => 'Nama tagihan ini sudah ada. Silakan gunakan nama lain.'
                ]
            ],
            'kelas' => 'required|in_list[7,8,9]',
            'jatuh_tempo'  => 'required|valid_date',
            'nominal'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tagihanModel->insert($data);
        $tagihanId = $tagihanModel->getInsertID();

        $siswaList = $siswaModel->where('kelas', $data['kelas'])->findAll();

        if (!empty($siswaList)) {
            $tagihanSppSiswaData = [];
            foreach ($siswaList as $siswa) {
                $tagihanSppSiswaData[] = [
                    'tagihan_id' => $tagihanId,
                    'nis'        => $siswa['nis'],
                    'status'     => 'Belum Lunas',
                ];
            }
            $tagihanSppSiswaModel->insertBatch($tagihanSppSiswaData);
        }

        return redirect()->to('admin/manajemen_spp')->with('success', 'Tagihan SPP berhasil dibuat dan diterapkan ke siswa kelas ' . esc($data['kelas']));
    }

    public function update($id)
    {
        $tagihanModel = new TagihanModel();
        $siswaModel = new SiswaModel();
        $tagihanSppSiswaModel = new TagihanSppSiswaModel();

        // 1. Ambil data dari form dan validasi
        $nominalBaru = (int) str_replace('.', '', $this->request->getPost('nominal'));
        $data = [
            'nama_tagihan' => $this->request->getPost('nama_tagihan'),
            'kelas'        => $this->request->getPost('kelas'),
            'jatuh_tempo'  => $this->request->getPost('jatuh_tempo'),
            'nominal'      => $nominalBaru,
        ];
        
        // MODIFIKASI: Aturan validasi dengan pesan kustom untuk update
        $rules = [
            'nama_tagihan' => [
                'rules' => "required|is_unique[tagihan_spp.nama_tagihan,id,{$id}]",
                'errors' => [
                    'required' => 'Nama tagihan wajib diisi.',
                    'is_unique' => 'Nama tagihan ini sudah ada. Silakan gunakan nama lain.'
                ]
            ],
            'kelas'        => 'required|in_list[7,8,9]',
            'jatuh_tempo'  => 'required|valid_date',
            'nominal'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data tagihan LAMA sebelum diupdate untuk perbandingan
        $tagihanLama = $tagihanModel->find($id);
        if (!$tagihanLama) {
            return redirect()->to('admin/manajemen_spp')->with('error', 'Tagihan yang akan diupdate tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 2. Update master tagihan terlebih dahulu
        $tagihanModel->update($id, $data);

        // 3. Lakukan Rekalkulasi Status jika nominal berubah
        if ($tagihanLama['nominal'] != $data['nominal']) {
            // Ambil SEMUA siswa yang terikat pada tagihan ini
            $semuaTagihanSiswa = $tagihanSppSiswaModel->where('tagihan_id', $id)->findAll();
            
            foreach ($semuaTagihanSiswa as $tagihanSiswa) {
                if ($tagihanSiswa['jumlah_bayar'] < $data['nominal']) {
                    // Gunakan variabel lokal $tagihanSppSiswaModel, bukan $this->
                    $tagihanSppSiswaModel->update($tagihanSiswa['id'], ['status' => 'Belum Lunas']);
                } else {
                    $tagihanSppSiswaModel->update($tagihanSiswa['id'], ['status' => 'Lunas']);
                }
            }
        }
        
        // 4. Proses jika ada perubahan kelas (logika lama yang disempurnakan)
        if ($tagihanLama['kelas'] != $data['kelas']) {
            // Hapus tagihan hanya untuk siswa di kelas LAMA yang belum bayar sama sekali
            $siswaDiKelasLama = $siswaModel->where('kelas', $tagihanLama['kelas'])->findAll();
            if (!empty($siswaDiKelasLama)) {
                $nisSiswaKelasLama = array_column($siswaDiKelasLama, 'nis');
                $tagihanSppSiswaModel->where('tagihan_id', $id)
                                     ->whereIn('nis', $nisSiswaKelasLama)
                                     ->where('jumlah_bayar', 0)
                                     ->delete();
            }
            
            // Tambahkan tagihan untuk siswa di kelas BARU yang belum punya tagihan ini
            $siswaDiKelasBaru = $siswaModel->where('kelas', $data['kelas'])->findAll();
            if (!empty($siswaDiKelasBaru)) {
                $nisYangSudahPunyaTagihan = array_column($tagihanSppSiswaModel->select('nis')->where('tagihan_id', $id)->findAll(), 'nis');
                $batchData = [];
                foreach ($siswaDiKelasBaru as $siswa) {
                    if (!in_array($siswa['nis'], $nisYangSudahPunyaTagihan)) {
                        $batchData[] = [
                            'tagihan_id' => $id,
                            'nis'        => $siswa['nis'],
                            'status'     => 'Belum Lunas',
                            'jumlah_bayar' => 0,
                        ];
                    }
                }
                if (!empty($batchData)) {
                    $tagihanSppSiswaModel->insertBatch($batchData);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('admin/manajemen_spp')->with('error', 'Gagal memperbarui tagihan. Terjadi kesalahan database.');
        } else {
            return redirect()->to('admin/manajemen_spp')->with('success', 'Data tagihan SPP berhasil diperbarui.');
        }
    }
}
