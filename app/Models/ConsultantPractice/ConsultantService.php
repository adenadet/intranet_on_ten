<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantService extends Structure
{
    use HasFactory;

    public const StatusActive = 1;
    public const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_consultant_services';

    protected $fillable = array('consultant_id', 'service_id', 'price', 'status', 'created_at', 'updated_at', 'deleted_at');

    public function consultant(){
        return $this->belongsTo('App\Models\ConsultantPractice\Consultant', 'consultant_id', 'id');
    }
    
    public function service(){
        return $this->belongsTo('App\Models\ConsultantPractice\Service', 'service_id', 'id');
    }

}
