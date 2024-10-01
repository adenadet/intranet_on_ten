<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Bank extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_banks';
    protected $fillable = array('bank_name', 'status' ); 
}
