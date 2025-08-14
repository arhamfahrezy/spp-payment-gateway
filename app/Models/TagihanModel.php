<?php
// C:\xampp\htdocs\payment-gateway\app\Models\TagihanModel.php
namespace App\Models;

use CodeIgniter\Model; 

class TagihanModel extends Model
{
    protected $table = 'tagihan_spp';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_tagihan', 'kelas', 'jatuh_tempo', 'nominal', 'status_tagihan',  'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
