<?php
// app/Models/PembayaranSppModel.php
namespace App\Models;

use CodeIgniter\Model;

class PembayaranSppModel extends Model
{
    protected $table            = 'pembayaran_spp';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nis',
        'tagihan_spp_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'status',
        'midtrans_order_id',
        'catatan',
        'snap_token' 
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
