<?php
namespace App\Models;

use CodeIgniter\Model;

class UangSakuModel extends Model
{
    protected $table = 'uang_saku';
    protected $primaryKey = 'nis';
    protected $allowedFields = ['nis', 'saldo', 'created_at', 'updated_at'];
}
