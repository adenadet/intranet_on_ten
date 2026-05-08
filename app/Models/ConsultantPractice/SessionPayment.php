<?php

namespace App\Models\ConsultantPractice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_session_payments';

    protected $fillable = array('session_id', 'decision', 'description', 'created_by', 'created_at', 'updated_at', 'deleted_at');

    public function session(){
        return $this->belongsTo('App\Models\ConsultantPractice\Session', 'session_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
