<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreArea extends Model
{
    protected $table = 'store_area';
    protected $primaryKey = 'area_id';

    protected $allowedFields = ['area_id', 'area_name'];
}