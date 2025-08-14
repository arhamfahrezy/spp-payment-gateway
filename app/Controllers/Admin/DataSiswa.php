<?php
// app/Controllers/Admin/DataSiswa.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\UsersModel;

class DataSiswa extends BaseController
{
    public function index()
    {
        $model = new SiswaModel();  

        // 1. Ambil input filter dari URL
        $filter = [
            'kelas'         => $this->request->getGet('kelas') ?? '',
            'jenis_kelamin' => $this->request->getGet('jenis_kelamin') ?? '',
            'search'        => $this->request->getGet('search') ?? '',
        ];

        // 2. Buat query builder dengan filter untuk MENGHITUNG JUMLAH
        // Kita akan menggunakan builder ini khusus untuk menghitung total
        $countBuilder = new SiswaModel(); // Buat instance baru untuk menghitung

        if (!empty($filter['kelas'])) {
            $countBuilder->like('kelas', $filter['kelas']);
        }
        if (!empty($filter['jenis_kelamin'])) {
            $countBuilder->where('jenis_kelamin', $filter['jenis_kelamin']);
        }
        if (!empty($filter['search'])) {
            $countBuilder->groupStart()
                ->like('nis', $filter['search'])
                ->orLike('nama_siswa', $filter['search'])
                ->orLike('nama_ayah', 'search')
                ->orLike('nama_ibu', $filter['search'])
            ->groupEnd();
        }

        // 3. Hitung jumlah berdasarkan query yang sudah difilter
        // Clone builder agar setiap perhitungan tidak saling tumpang tindih
        $jumlahSiswa = (clone $countBuilder)->countAllResults();
        $jumlahLaki = (clone $countBuilder)->where('jenis_kelamin', 'Laki-laki')->countAllResults();
        $jumlahPerempuan = (clone $countBuilder)->where('jenis_kelamin', 'Perempuan')->countAllResults();
        
        // 4. Buat query terpisah untuk MENGAMBIL DAFTAR SISWA (untuk ditampilkan di tabel)
        // Ini memastikan query untuk daftar tidak tercampur dengan query untuk hitung jumlah
        $listBuilder = new SiswaModel();
        if (!empty($filter['kelas'])) {
            $listBuilder->like('kelas', $filter['kelas']);
        }
        if (!empty($filter['jenis_kelamin'])) {
            $listBuilder->where('jenis_kelamin', $filter['jenis_kelamin']);
        }
        if (!empty($filter['search'])) {
            $listBuilder->groupStart()
                ->like('nis', $filter['search'])
                ->orLike('nama_siswa', $filter['search'])
                ->orLike('nama_ayah', $filter['search'])
                ->orLike('nama_ibu', $filter['search'])
            ->groupEnd();
        }

        $siswaList = $listBuilder->orderBy('nis', 'ASC')->orderBy('kelas', 'ASC')->findAll();

        // Siapkan data untuk dikirim ke view
        $data = [
            'title'           => 'Manajemen Data Siswa',
            'siswaList'       => $siswaList,
            'filter'          => $filter,
            'jumlahSiswa'     => $jumlahSiswa,
            'jumlahLaki'      => $jumlahLaki,
            'jumlahPerempuan' => $jumlahPerempuan,
        ];
        
        return view('admin/data_siswa', $data);
    }

    public function hapus($nis)
    {
        $siswaModel = new SiswaModel();
        $usersModel = new UsersModel();

        // Ambil data siswa untuk mendapatkan user_id terkait
        $siswa = $siswaModel->find($nis);

        // Jika data siswa tidak ditemukan, kembali dengan pesan error
        if (!$siswa) {
            return redirect()->to('admin/data_siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Mulai database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Hapus data dari tabel 'siswa'
        $siswaModel->delete($nis);

        // Jika ada user_id yang terhubung, hapus juga dari tabel 'users'
        if (!empty($siswa['user_id'])) {
            $usersModel->delete($siswa['user_id']);
        }

        // Selesaikan transaction
        $db->transComplete();

        // Cek status transaksi
        if ($db->transStatus() === FALSE) {
            // Jika transaksi gagal, kembalikan pesan error
            return redirect()->to('admin/data_siswa')->with('error', 'Gagal menghapus data siswa dan akun terkait.');
        } else {
            // Jika transaksi berhasil, kembalikan pesan sukses
            return redirect()->to('admin/data_siswa')->with('success', 'Data siswa dan akun pengguna terkait berhasil dihapus.');
        }
    }
}