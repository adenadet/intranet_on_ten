<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BureauProduct extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_credit_bureau_products';
    protected $fillable = array('name', 'bureau_id', 'bureau_product_id', 'link');

    public function bureau(){
        return $this->hasOne('App\Models\Finance\Bureau', 'id','bureau_id');
    }
}
