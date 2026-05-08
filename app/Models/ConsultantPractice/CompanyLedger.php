<?php

namespace App\Models\ConsultantPractice;

use Illuminate\Database\Eloquent\Model;

class CompanyLedger extends Model
{
    protected $table = 'consultant_practice_company_ledgers';

    protected $fillable = ['company_id', 'type', 'amount', 'balance', 'reference_type', 'reference_id', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}