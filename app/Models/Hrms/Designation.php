<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_designations';
    protected $fillable = array('top_designation_id', 'department_id', 'sub_department_id', 'company_id', 'name', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}
