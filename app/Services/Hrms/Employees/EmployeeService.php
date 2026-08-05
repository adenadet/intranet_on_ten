<?php
namespace App\Services\Hrms;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\FileManagerTrait;

use App\Models\EMR\Patient;
use App\Models\Hrms\Employee;
use App\Models\User;
use App\Services\Ums\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class EmployeeService{

    public function __construct(
        protected UserService $ums, 
    ) {}

    protected function userId(){
        return auth('api')->id() ?? Auth::id();
    }

    public function assign_manager(array $data, int|string $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $employee = Employee::find($id);
            $employee->update([
                'supervisor_id' => $data['supervisor_id'],
                'reports_to' => $data['reports_to'],
            ]);

            return $employee;
        });
    }

    public function changeStatus(array $data, int|string $id){
        return DB::transaction(function () use ($data, $id) {
            $employee = Employee::findOrFail($id);
            $employee->update([
                'employment_status' => $data['employment_status'],
                'date_of_leaving' => ($data['employment_status'] == 1) ? null : $data['date_of_leaving'],
                'updated_by' => $this->userId(),
            ]);
            return $employee;
        });
    }

    public function create(array $data){
        return DB::transaction(function () use ($data) {
            $user = $data['from_user'] == 'new' ? $this->ums->create($data['user']) : User::find($data['user_id']);
            $user->assignRole('Staff');
            
            $number = Employee::max('employee_id');
            $number++;
            
            $query = Employee::create([
                'user_id' => $user->id,
                'employee_id' => $number,
                'office_shift_id' => NULL,
                'reports_to' => $data['reports_to'],
                'supervisor_id' => $data['supervisor_id'],
                'username' => $user->unique_id ?? 'SNH-'.$number,
                'email' => $data['email'],
                'department_id' => $data['department_id'],
                'sub_department_id' => NULL,
                'designation_id' => $data['designation_id'],
                'date_of_joining' => $data['date_of_joining'] ?? date('Y-m-d'),
                'date_of_leaving' => $data['date_of_leaving'] ?? NULL,
                'employment_status' => $data['employment_status'] ?? Employee::EmploymentStatusActive,
                'created_by' => $this->userId(),
                'updated_by' => $this->userId(),
            ]);

            return $query;
        });
    }

    public function deactivate(int|string $id){
        return DB::transaction(function () use ($id) {
            $employee = Employee::where('id', '=', $id)->first();
            
            $employee->update([
                'date_of_leaving' => date('Y-m-d'),
                'employment_status' => Employee::EmploymentStatusInactive,
                'deleted_by' => $this->userId(),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            
            return $employee;
        });
    }

    public function update(array $data, int|string $id){
        return DB::transaction(function () use ($data, $id) {
            $employee = Employee::find($id);

            $employee->update([
                'date_of_joining'  => $data['date_of_joining'] ?? $employee->date_of_joining,
                'date_of_leaving'  => $data['date_of_leaving'] ?? $employee->date_of_leaving,
                'department_id'    => $data['department_id'] ?? $employee->department_id,
                'designation_id'   => $data['designation_id'] ?? $employee->designation_id,
                'email'            => $data['email'] ?? $employee->email,
                'employee_id'      => $data['employee_id'] ?? $employee->employee_id,
                'office_shift_id'  => $data['office_shift_id'] ?? $employee->office_shift_id,
                'reports_to'       => $data['reports_to'] ?? $employee->reports_to,
                'sub_department_id'=> $data['sub_department_id'] ?? $employee->sub_department_id,
                'supervisor_id'    => $data['supervisor_id'] ?? $employee->supervisor_id,
                'user_id'          => $data['user_id'] ?? $employee->user_id,
                'username'         => $data['username'] ?? $employee->username,
            ]);
            
            return $employee;
        });
    }

}