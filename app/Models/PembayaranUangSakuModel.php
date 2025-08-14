<?php
// app/Models/PembayaranUangSakuModel.php
namespace App\Models;

use CodeIgniter\Model;

class PembayaranUangSakuModel extends Model
{
    protected $table            = 'pembayaran_uang_saku';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nis',
        'jumlah_bayar',
        'catatan_walimurid',
        'status',
        'midtrans_order_id',
        'snap_token'
    ];
    protected $useTimestamps    = true;
}
?>