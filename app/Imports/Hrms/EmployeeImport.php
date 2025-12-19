<?php

namespace App\Imports\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Models\Hrms\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class EmployeeImport implements ToModel, WithHeadingRow
{
    use EmployeeTrait, LogTrait;
    public function model(array $row)
    {
        $number = Employee::max('employee_id');
        $number++;
        $unique = !empty($row['unique_id']) ? str_replace('SNH-', '', $row['unique_id']) : $number;

        $line_manager = $supervisor = null;
        if(!empty($row['supervisor_snh_id'])){
            $sup_employee = Employee::where('username', '=', $row['supervisor_snh_id'])->first();
            if($sup_employee){
                $supervisor = $sup_employee->employee_id;
            }
        }
        if(!empty($row['line_manager_snh_id'])){
            $lm_employee = Employee::where('username', '=', $row['line_manager_snh_id'])->first();
            if($lm_employee){
                $line_manager = $lm_employee->employee_id;
            }
        } 
        $user = User::create([
            'email' => $row['email'] ?? null,
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'] ?? null,
            'last_name' => $row['last_name'],
            'street' => $row['street'] ?? null,
            'street2' => $row['street2'] ?? null,
            'city' => $row['city'] ?? null,
            'state_id' => $row['state_id'] ?? 25,
            'area_id' => $row['area_id'] ?? 0,
            'personal_email' => $row['personal_email']  ?? null,
            'phone' => $row['phone'] ?? null,
            'alt_phone' => $row['alt_phone'] ?? null,
            'department_id' => $row['department_id'] ?? null,
            'sex' => $row['sex']  ?? null,
            'dob' => $row['dob'] ?? null,
            'image' => 'default.png',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'unique_id' => $row['unique_id'] ?? 'SNH-'.$unique,
            'password' => bcrypt('asdfasdf'),
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => $unique,
            'office_shift_id' => NULL,
            'reports_to' => $line_manager ?? null,
            'supervisor_id' => $supervisor ?? null,
            'username' => $user->unique_id ?? 'SNH-'.$unique,
            'email' => $row['email'] ?? null,
            'department_id' => $row['department_id'] ?? null,
            'sub_department_id' => NULL,
            'designation_id' => $row['designation_id'] ?? null,
            'date_of_joining' => $row['date_of_joining'] ?? date('Y-m-d'),
            'date_of_leaving' => $row['date_of_leaving'] ?? NULL,
            'employment_status' => $row['employment_status'] ?? 1,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);

        $this->log_user_activity('New Employee Created', $employee->id, true);
        return $employee;
    }
}
