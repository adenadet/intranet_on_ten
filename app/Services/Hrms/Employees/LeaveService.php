<?php
namespace App\Services\Hrms;

use App\Mail\Leave\ConfirmMail;
use App\Mail\Leave\RejectMail;
use App\Mail\Leave\RequestMail;
use App\Mail\Leave\SupervisorConfirmMail;
use App\Mail\Leave\SupervisorInfoMail;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\LeaveRequest;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\PublicHoliday;
use App\Models\User;
use App\Services\Hrms\AllowanceService;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class LeaveService{
    public function __construct(
        protected AllowanceService $as, 
    ) {}

    protected function request_number_of_days($leave_request)
    {
        $leave_type = LeaveType::find($leave_request->leave_type_id);

        $start_date = new DateTime($leave_request->start_date);
        $end_date   = new DateTime($leave_request->to_date);

        $days = 0;

        if ($leave_type->leave_category == 'Working') {

            if (!defined('SATURDAY')) define('SATURDAY', 6);
            if (!defined('SUNDAY')) define('SUNDAY', 0);

            $publicHolidays = PublicHoliday::whereDate('date', '>=', $leave_request->start_date)
                ->whereDate('date', '<=', $leave_request->to_date)
                ->pluck('date')
                ->map(fn($date) => date('Y-m-d', strtotime($date)))
                ->toArray();

            $yearStart = date('Y', strtotime($leave_request->start_date));
            $yearEnd   = date('Y', strtotime($leave_request->to_date));

            $easterMondays = [];

            for ($i = $yearStart; $i <= $yearEnd; $i++) {

                $easter = date('Y-m-d', easter_date($i));

                list($y, $m, $g) = explode("-", $easter);

                $easterMondays[] = date(
                    'Y-m-d',
                    mktime(0, 0, 0, $m, $g + 1, $y)
                );
            }
            $start = strtotime($leave_request->start_date);
            $end   = strtotime($leave_request->to_date);
            $workdays = 0;
            for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
                $day = date("w", $i);
                $currentDate = date('Y-m-d', $i);
                if ($day != SUNDAY && $day != SATURDAY && !in_array($currentDate, $publicHolidays) && !in_array($currentDate, $easterMondays)) {$workdays++;}
            }
            $days = intval($workdays);
        } 
        else {
            $interval = $start_date->diff($end_date);
            $days = $leave_request->is_half_day ? (($interval->format('%a') + 1) / 2) : ($interval->format('%a') + 1);
        }
        return $days;
    }

    public function confirm(array $data, string|int $id){
        return DB::transaction(function () use ($data, $id) {
            $leave_request = LeaveRequest::where('id', '=', $id)->first();
            //print($leave_request->status);
            if ($data['action'] == 'confirm'){
                //Update the leave request to confirmed 
                $leave_request->status = 1;
                $leave_request->approval_remark = $data['remark'];
                $leave_request->approved_by = auth('api')->id();
                $leave_request->approved_at = date('Y-m-d H:i:s');
                $leave_request->save();

                $days = $this->request_number_of_days($leave_request);
                
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days -= $days;
                $employee_leave_type->days_used += $days;
                $employee_leave_type->save();

                $employee = Employee::where('id', '=', $leave_request->employee_id)->with(['user'])->first();
                //send mail to line manager 
                $line_manager = Employee::where('user_id', '=', auth('api')->id() ?? Auth::id())->with(['user'])->first();
                if ($employee){
                    if (!(is_null($employee->email))){
                        $mailed = Mail::to($employee->email)->send(new ConfirmMail($leave_request, $employee->user, $line_manager->user, $days, $data['message']));
                    }
                
                    $supervisor = Employee::where('employee_id', '=', $employee->supervisor_id)->with(['user'])->first();
                    if ($supervisor){
                        if (!(is_null($supervisor->email))){
                            $mailed = Mail::to($supervisor->email)->send(new SupervisorConfirmMail($leave_request, $employee, $supervisor->user, $line_manager->user));
                        }
                    }
                }
                //send mail to supervisor
                
                if ($leave_request->leave_allowance) {
                    $this->as->create([
                        'employee_id' => $leave_request->employee_id,
                        'leave_request_id' => $leave_request->id,
                    ]);
                }
                //$this->log_user_activity('leave_request_confirm', $id, true);
            }
            else if ($data['action'] == 'reject'){
                $leave_request->status = 10;
                $leave_request->approval_remark = $data['remark'];
                $leave_request->approved_by = auth('api')->id();
                $leave_request->approved_at = date('Y-m-d H:i:s');
                $leave_request->save();

                $days = $this->request_number_of_days($leave_request);
                
                //Update Employee Leave Type to ensure the dates are aligned 
                $employee_leave_type = EmployeeLeaveType::where('user_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days -= $days;
                $employee_leave_type->save();

                $employee = Employee::find($leave_request->employee_id);
                //send mail to line manager 
                $line_manager = Employee::where('user_id', '=', auth('api')->id() ?? Auth::id())->with(['user'])->first();
                if ($employee){
                    if (!(is_null($employee->email))){
                        $mailed = Mail::to($employee->email)->send(new RejectMail($leave_request, $employee->user, $line_manager->user, $days, $data['message']));
                    }
                }

            }
        
            DB::commit();
            return $leave_request;
        });    
    }
    public function create(array $data){
        return DB::transaction(function () use ($data) {
            if (isset($data['employee_id'])){$employee = Employee::findOrFail($data['employee_id']);}
            else{$employee = Employee::where('user_id', '=', (Auth::id() ?? auth('api')->id()))->first();}

            if (isset($data['leave_type_id'])){$user_leave_type = EmployeeLeaveType::find($data['leave_type_id']);}
            else{$user_leave_type = EmployeeLeaveType::where('employee_id', '=', $data['employee_id'])->where('leave_type_id', '=', $data['leave_id'])->first();}

            $leave_attachment = null;
           
            $leave_request = LeaveRequest::create([
                'employee_id' => $employee->id,
                'user_leave_type_id' => $user_leave_type->id, 
                'leave_type_id' => $user_leave_type->leave_type_id,
                'from_date' => $data['from_date'], 
                'to_date' => $data['to_date'], 
                'leave_allowance' => $data['leave_allowance'] ? 1 : 0,
                'reason' => $data['reason'], 
                'remarks' => $data['remarks'], 
                'status' => $data['status'] ?? 0, 
                'is_half_day' => $data['is_half_day'] ?? 0, 
                'leave_attachment' => $leave_attachment ?? NULL, 
                'created_by' => auth('api')->id(), 
                'updated_by' => auth('api')->id(), 
            ]);

            //if creation successful, 
            $days = $this->request_number_of_days($leave_request);
                
                $data = Array();
                $data['days'] = $days;
                //send mail to line manager 
            $line_manager = Employee::where('employee_id', '=', $employee->reports_to)->with(['user'])->first();
            if ($line_manager){
                //$line_manager->user->notify(new Created($data, $employee, $leave_request, $line_manager, null));
                if (!(is_null($line_manager->email))){$mailed = Mail::to($line_manager->email)->send(new RequestMail($leave_request, $employee, $line_manager->user));}
            }
                //send mail to supervisor
            $supervisor = Employee::where('employee_id', '=', $employee->supervisor_id)->with(['user'])->first();
            if (isset($supervisor)){
                if (!(is_null($supervisor->email))){$mailed = Mail::to($supervisor->email)->send(new SupervisorInfoMail($leave_request, $employee, $supervisor->user, $line_manager->user));}
            }
                
            $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
            $employee_leave_type->pending_days += $days;
            
            $employee_leave_type->save();

            return $leave_request;
        });    
    }
    
    public function delete(int|string $id){
        DB::beginTransaction();

        try{
            $leave_request = LeaveRequest::where('id', '=', $id)->where('status', '=', 1)->first();
            if ($leave_request){
                $leave_request->status = 2;
                $leave_request->deleted_by = auth('api')->id();
                $leave_request->deleted_at = date('Y-m-d H:i:s');
                $leave_request->save();

                $days = $this->request_number_of_days($leave_request);
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days += $days;
                $employee_leave_type->save();
            
                //$this->log_user_activity('leave_request_delete', $id, true);
                $complete = true;
            }
            else{
                //$this->log_user_activity('leave_request_delete', $id, false);
                $complete = false;    
            }
        }
        catch (Exception $e){
            //$this->log_user_activity('leave_request_delete', $id, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            return $leave_request;
        }
        else{
            DB::rollBack();
        }     
    }

    public function update(array $data, int|string $id){
        return DB::transaction(function () use ($data, $id) {
            $old_request = $leave_request = LeaveRequest::findOrFail($id);
            
            // Update leave first 
            $leave_request->update([
                'leave_type_id' => $data['leave_type_id'],
                'from_date' => $data['from_date'], 
                'to_date' => $data['to_date'],
                'applied_on' => $data['applied_on'], 
                'reason' => $data['reason'], 
                'remarks' => $data['remarks'], 
                'status' => $data['status'] ?? 1, 
                'is_half_day' => $data['is_half_day'], 
                'is_notify' => 0,
                'leave_attachment' => $leave_attachment ?? NULL, 
                'updated_by' => auth('api')->id(),
            ]);

            // Update employee leave type to ensure the dates are aligned
            $days = $this->request_number_of_days($leave_request);
            $old_days = $this->request_number_of_days($old_request);
            $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
            $employee_leave_type->pending_days += ($days - $old_days);
            $employee_leave_type->save();
               
            $leave_request->save();
            $employee = Employee::where('employee_id', '=', $leave_request->employee_id)->with('user')->first();
            
        
            //send mail to line manager 
            $supervisor = Employee::where('id', '=', $employee->supervisor_id)->with(['user'])->first();
            $line_manager = $supervisor->user;
            
            //try to send notification to line manager not a breaking issue
            if (!(is_null($line_manager->email))){$mailed = Mail::to($line_manager->email)->send(new RequestMail($leave_request, $employee, $line_manager));}
        });
    }
}