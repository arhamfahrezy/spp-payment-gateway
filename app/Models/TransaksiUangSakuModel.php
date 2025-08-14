<?php
namespace App\Models;

use CodeIgniter\Model;

class TransaksiUangSakuModel extends Model
{
    protected $table = 'transaksi_uang_saku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nis', 'tipe_transaksi', 'nominal', 'tanggal', 'catatan', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
