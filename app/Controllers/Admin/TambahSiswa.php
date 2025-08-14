<?php
// C:\xampp\htdocs\payment-gateway\app\Controllers\Admin\TambahSiswa.php 
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\UsersModel;
use App\Models\TagihanModel;         
use App\Models\TagihanSppSiswaModel; 

class TambahSiswa extends BaseController
{
    public function index()
    {
        return view('admin/tambah_siswa');
    }

    public function simpan()
    {
        $usersModel = new UsersModel();
        $siswaModel = new SiswaModel();
        $tagihanModel = new TagihanModel();                 
        $tagihanSppSiswaModel = new TagihanSppSiswaModel(); 

        $request = $this->request;

        // Aturan validasi (bisa ditambahkan sesuai kebutuhan)
        $rules = [
            'nis' => [
                'rules' => 'required|is_unique[siswa.nis]',
                'errors' => [
                    'required' => 'NIS wajib diisi.',
                    'is_unique' => 'NIS ini sudah terdaftar. Silakan gunakan NIS lain.'
                ]
            ],
            'nama_siswa' => [
                'rules' => 'required|alpha_space|min_length[3]',
                'errors' => [
                    'required' => 'Nama siswa wajib diisi.',
                    'alpha_space' => 'Nama siswa hanya boleh berisi huruf dan spasi.',
                    'min_length' => 'Nama siswa minimal 3 karakter.'
                ]
            ],
            'kelas' => [
                'rules' => 'required|in_list[7,8,9]',
                'errors' => [
                    'required' => 'Kelas wajib dipilih.',
                    'in_list' => 'Kelas tidak valid.'
                ]
            ],
            'tanggal_lahir' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal lahir wajib diisi.',
                    'valid_date' => 'Format tanggal lahir tidak valid.'
                ]
            ],
            'jenis_kelamin' => [
                'rules' => 'required|in_list[Laki-laki,Perempuan]',
                'errors' => [
                    'required' => 'Jenis kelamin wajib dipilih.',
                    'in_list' => 'Jenis kelamin tidak valid.'
                ]
            ],
            'nama_ayah' => [
                'rules' => 'required|alpha_space|min_length[3]',
                'errors' => [
                    'required' => 'Nama ayah wajib diisi.',
                    'alpha_space' => 'Nama ayah hanya boleh berisi huruf dan spasi.',
                    'min_length' => 'Nama ayah minimal 3 karakter.'
                ]
            ],
            'nama_ibu' => [
                'rules' => 'required|alpha_space|min_length[3]',
                'errors' => [
                    'required' => 'Nama ibu wajib diisi.',
                    'alpha_space' => 'Nama ibu hanya boleh berisi huruf dan spasi.',
                    'min_length' => 'Nama ibu minimal 3 karakter.'
                ]
            ],
            'wali_murid_pilihan' => [
                'rules' => 'required|in_list[ayah,ibu]',
                'errors' => [
                    'required' => 'Pilihan wali murid wajib ditentukan.',
                    'in_list' => 'Pilihan wali murid tidak valid.'
                ]
            ],
            'email_orangtua' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email wali murid wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email ini sudah terdaftar untuk wali murid lain.'
                ]
            ],
            'password_orangtua' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 6 karakter.'
                ]
            ],
            'telepon_orangtua' => [
                'rules' => 'permit_empty|numeric|min_length[10]|max_length[15]',
                'errors' => [
                    'numeric' => 'Nomor telepon hanya boleh berisi angka.',
                    'min_length' => 'Nomor telepon minimal 10 digit.',
                    'max_length' => 'Nomor telepon maksimal 15 digit.'
                ]
            ],
            'provinsi' => 'required',
            'kota' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'alamat_detail' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai database transaction untuk memastikan semua data tersimpan atau tidak sama sekali
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Simpan data user (wali murid)
        $waliMurids = $request->getPost('wali_murid_pilihan');
        $namaAyah = $request->getPost('nama_ayah');
        $namaIbu = $request->getPost('nama_ibu');
        $password = $request->getPost('password_orangtua');
        $namaWali = ($waliMurids === 'ayah') ? $namaAyah : $namaIbu;
        $userData = [
            'nama' => $namaWali,
            'email' => $request->getPost('email_orangtua'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'level' => 'walimurid'
        ];
        $usersModel->insert($userData);
        $userId = $usersModel->getInsertID();
        $nis = $request->getPost('nis');
        $kelas = $request->getPost('kelas');
        $siswaData = [
            'nis' => $nis,
            'user_id' => $userId,
            'nama_siswa' => $request->getPost('nama_siswa'),
            'kelas' => $kelas,
            'tanggal_lahir' => $request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $request->getPost('jenis_kelamin'),
            'nama_ayah' => $namaAyah,
            'nama_ibu' => $namaIbu,
            'telepon_orangtua' => $request->getPost('telepon_orangtua'),
            'provinsi' => $request->getPost('provinsi'),
            'kota' => $request->getPost('kota'),
            'kecamatan' => $request->getPost('kecamatan'),
            'kelurahan' => $request->getPost('kelurahan'),
            'alamat_detail' => $request->getPost('alamat_detail'),
        ];
        $siswaModel->insert($siswaData);

        // 3. MODIFIKASI: Terapkan tagihan yang sudah ada untuk siswa baru
        $tagihanUntukKelas = $tagihanModel->where('kelas', $kelas)->findAll();
        if (!empty($tagihanUntukKelas)) {
            $batchDataTagihanSiswa = [];
            foreach ($tagihanUntukKelas as $tagihan) {
                $batchDataTagihanSiswa[] = [
                    'tagihan_id' => $tagihan['id'],
                    'nis'        => $nis,
                    'status'     => 'Belum Lunas',
                ];
            }
            $tagihanSppSiswaModel->insertBatch($batchDataTagihanSiswa);
        }

        // Selesaikan transaksi
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data siswa karena kesalahan database.');
        }

        return redirect()->to('/admin/data_siswa')->with('success', 'Data siswa baru berhasil ditambahkan dan semua tagihan yang relevan telah diterapkan.');
    }

    // Method menampilkan form edit dengan data siswa sudah terisi
    public function edit($nis)
    {
        $siswaModel = new SiswaModel();
        $usersModel = new UsersModel();

        $siswa = $siswaModel->find($nis);

        if (!$siswa) {
            return redirect()->to('/admin/data_siswa')->with('error', 'Data siswa tidak ditemukan.');
        }
        // Ambil data user (wali murid) berdasarkan user_id siswa
        $user = $usersModel->find($siswa['user_id']);
        $password = $usersModel->find($siswa['user_id']);
        
        // Kirim data ke view
        return view('admin/tambah_siswa', [ 
            'siswa' => $siswa,
            'email_orangtua' => $user['email'] ?? '',
            'password_placeholder' => $password['password'] ?? '',
            // jika ada info siapa wali murid, misal 'wali_murid' => 'ayah' atau 'ibu'
            // kamu bisa kirimkan juga, misal:
            // 'wali_murid' => $this->getWaliMuridSelection($siswa) // method custom yang bisa kamu buat
        ]);
    }

    // Method untuk update dengan data siswa sudah terisi
    public function update($nis)
{
    // Inisialisasi semua model yang dibutuhkan
    $siswaModel = model(SiswaModel::class);
    $usersModel = model(UsersModel::class);
    $tagihanModel = model(TagihanModel::class);
    $tagihanSppSiswaModel = model(TagihanSppSiswaModel::class);

    $request = $this->request;

    // Ambil data siswa yang akan diupdate
    $siswaLama = $siswaModel->find($nis);
    if (!$siswaLama) {
        return redirect()->to('/admin/data_siswa')->with('error', 'Data siswa tidak ditemukan.');
    }

    // TODO: Tambahkan aturan validasi untuk form edit di sini jika diperlukan
    // ...

    // Mulai database transaction
    $db = \Config\Database::connect();
    $db->transStart();

    // 1. Update data user wali murid
    $userId = $siswaLama['user_id'];
    $userData = [
        'nama' => ($request->getPost('wali_murid_pilihan') === 'ayah') ? $request->getPost('nama_ayah') : $request->getPost('nama_ibu'),
        'email' => $request->getPost('email_orangtua'),
    ];
    $passwordBaru = $request->getPost('password_orangtua');
    if (!empty($passwordBaru)) {
        $userData['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
    }
    $usersModel->update($userId, $userData);

    // 2. Siapkan data update siswa
    $kelasBaru = $request->getPost('kelas');
    $siswaData = [
        'nama_siswa' => $request->getPost('nama_siswa'),
        'kelas' => $kelasBaru, // Gunakan kelas baru
        'tanggal_lahir' => $request->getPost('tanggal_lahir'),
        'jenis_kelamin' => $request->getPost('jenis_kelamin'),
        'nama_ayah' => $request->getPost('nama_ayah'),
        'nama_ibu' => $request->getPost('nama_ibu'),
        'telepon_orangtua' => $request->getPost('telepon_orangtua'),
        'provinsi' => $request->getPost('provinsi'),
        'kota' => $request->getPost('kota'),
        'kecamatan' => $request->getPost('kecamatan'),
        'kelurahan' => $request->getPost('kelurahan'),
        'alamat_detail' => $request->getPost('alamat_detail'),
    ];
    $siswaModel->update($nis, $siswaData);

    // 3. LOGIKA CERDAS: Cek jika ada perubahan kelas
    if ($siswaLama['kelas'] != $kelasBaru) {
        // A. Hapus semua tagihan siswa ini yang statusnya 'Belum Lunas'
        $tagihanSppSiswaModel->where('nis', $nis)->where('status', 'Belum Lunas')->delete();

        // B. Ambil dan terapkan semua tagihan untuk kelas yang BARU
        $tagihanUntukKelasBaru = $tagihanModel->where('kelas', $kelasBaru)->findAll();
        if (!empty($tagihanUntukKelasBaru)) {
            $batchDataTagihanSiswa = [];
            foreach ($tagihanUntukKelasBaru as $tagihan) {
                $batchDataTagihanSiswa[] = [
                    'tagihan_id' => $tagihan['id'],
                    'nis'        => $nis,
                    'status'     => 'Belum Lunas',
                ];
            }
            $tagihanSppSiswaModel->insertBatch($batchDataTagihanSiswa);
        }
    }

    // Selesaikan transaksi
    $db->transComplete();

    if ($db->transStatus() === false) {
        // Jika ada yang gagal, kembalikan dengan pesan error
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data siswa karena kesalahan database.');
    }

    return redirect()->to('/admin/data_siswa')->with('success', 'Data siswa berhasil diperbarui.');
}

}
