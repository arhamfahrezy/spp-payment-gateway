<?php
// C:\xampp\htdocs\payment-gateway\app\Models\TagihanSppSiswaModel.php
namespace App\Models;

use CodeIgniter\Model;

class TagihanSppSiswaModel extends Model
{
    protected $table = 'tagihan_spp_siswa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tagihan_id', 'nis', 'status', 'jumlah_bayar', 'tanggal_bayar', 'catatan_walimurid', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
