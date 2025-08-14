<?php 
namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'nis';

    protected $allowedFields = [
        'nis',
        'user_id',
        'nama_siswa',
        'kelas',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_ayah',
        'nama_ibu',
        'telepon_orangtua',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'alamat_detail',
        'created_at',
        'updated_at'
    ];

    // Fungsi hitung total siswa
    public function getTotalSiswa()
    {
        return $this->countAllResults();
    }

    // Fungsi hitung siswa laki-laki
    public function getTotalSiswaLaki()
    {
        return $this->where('jenis_kelamin', 'Laki-laki')->countAllResults();
    }

    // Fungsi hitung siswi perempuan
    public function getTotalSiswaPerempuan()
    {
        return $this->where('jenis_kelamin', 'Perempuan')->countAllResults();
    }

    // Fungsi ambil semua siswa
    public function getAllSiswa()
    {
        return $this->findAll();
    }

    // Fungsi siswa dari kelas
    public function getSiswaByKelas($kelas)
    {
        return $this->where('kelas', $kelas)->findAll();
    }
}
 