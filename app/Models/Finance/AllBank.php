<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllBank extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'all_banks';
    protected $fillable = array('name', 'status',  'deleted_by', 'deleted_at');
}
