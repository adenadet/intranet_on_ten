<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAllowance extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_leave_allowances';
    protected $fillable = array('employee_id', 'leave_request_id', 'status', 'amount', 'approved_by', 'approved_at', 'approval_remark', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function approver(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'approved_by', 'employee_id');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

    public function employee_leave(){
        return $this->belongsTo('App\Models\Hrms\LeaveRequest', 'leave_request_id', 'id');
    }
}
