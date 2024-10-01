<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_employees';
    protected $fillable = array('user_id', 'employee_id', 'office_shift_id', 'reports_to', 'supervisor_id', 'username', 'email', 'department_id', 'sub_department_id', 'designation_id', 'date_of_joining', 'date_of_leaving', 'employment_status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function department(){
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    public function designation(){
        return $this->belongsTo('App\Models\Hrms\Designation', 'designation_id', 'id');
    }
    public function line_manager(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'reports_to', 'employee_id');
    }

    public function supervisor(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'supervisor_id', 'employee_id');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    
}
