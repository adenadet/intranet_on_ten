<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bureau extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_credit_bureaus';
    protected $fillable = array('bank_name', 'status' );
}
