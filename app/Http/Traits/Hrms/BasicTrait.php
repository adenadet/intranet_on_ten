<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\LogTrait;

use App\Models\Hrms\AttendanceSummary;
use App\Models\Hrms\Branch;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\Leave;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\OrganizationHierarchy;
use App\Models\Hrms\PublicHoliday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Mail;
use App\Mail\AdminApplyLeaveMail;
use App\Mail\ApplyLeaveMail;
use App\Mail\LeaveStatusMail;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;


trait BasicTrait{
    //Departments

    public function hrms_basic_confirm_basic_request($request, $id){}

    public function hrms_basic_create_basic_request($request){}

    public function hrms_basic_get_all_my_basic_requests($user_id){}

    public function hrms_basic_get_all_pending_basic_requests(){}
    
    public function hrms_basic_get_my_team_members_basic_requests($team_members){}

    public function hrms_basic_reject_basic_request($request, $id){}

    public function hrms_basic_types_create($request){}

    public function hrms_basic_types_update($request, $id){}

    public function hrms_basic_types_delete_by_id($request){}

    public function hrms_basic_types_get_all(){}

    public function hrms_basic_types_get_my_current_basic_types($user_id){}

    public function hrms_basic_public_holiday_create($data){
        DB::beginTransaction();

        try{
            $public_holiday = PublicHoliday::whereDate('date', '=', $data['date'])->withTrashed()->first();
            
            if($public_holiday){
                $public_holiday->status = $data['status'] ?? 1;
                $public_holiday->updated_by = auth('api')->id() ?? Auth::id(); 
                $public_holiday->deleted_by = null; 
                $public_holiday->deleted_at = null; 

                $public_holiday->save();
            }
            else{
                $public_holiday = PublicHoliday::create([
                    'date' => $data['date'],
                    'status' => $data['status'] ?? 1,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            DB::commit();
            return $public_holiday;
        }
        catch(Exception $e){
            DB::rollBack();
            //app('log')->logError('HRMS Basic Trait - Public Holiday Create : '.$e->getMessage());
            return $e->getMessage();
        }
    }

    public function hrms_basic_public_holiday_deactivate($id){
        DB::beginTransaction();

        try{
            $public_holiday = PublicHoliday::where('id', '=', $id)->withTrashed()->firstOrFail();
            if($public_holiday->status = 1){
                $public_holiday->status = 0;
                $public_holiday->deleted_by = auth('api')->id() ?? Auth::id();
                $public_holiday->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $public_holiday->status = 1;
                $public_holiday->deleted_by = null;
                $public_holiday->deleted_at = null;
            }
            $public_holiday->updated_by = auth('api')->id() ?? Auth::id();
            $public_holiday->save();

            DB::commit();
            return $public_holiday;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function hrms_basic_public_holiday_get_all($type, $specific, $detailed, $paginated){
        $query = PublicHoliday::query();

        if (is_array($specific)){
            if(!empty($specific['start_date'])){
                $query = $query->whereDate('date',  '>=', $specific['start_date']);
            }
            if(!empty($specific['end_date'])){
                $query = $query->whereDate('date',  '<=', $specific['end_date']);
            }
        }

        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->pluck('date');
        $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_basic_public_holiday_get_by($id){
        try{
            $public_holiday = PublicHoliday::where('id', '=', $id)->orWhere('date', '=', $id);
            $public_holiday = $public_holiday->with(['creator', 'updater', 'deleter'])->firstOrFail();
            return $public_holiday;
        }
        catch(Exception $e){
            //app('log')->logError('HRMS Basic Trait - Public Holiday Get By ID : '.$e->getMessage());
            return $e->getMessage();
        }
    }

    public function hrms_basic_public_holiday_update($data, $id){
        DB::beginTransaction();

        try{
            $public_holiday = PublicHoliday::findOrFail($id);
            $public_holiday->date = $data['date'] ?? $public_holiday->date;
            $public_holiday->status = $data['status'] ?? $public_holiday->status;
            $public_holiday->updated_by = auth('api')->id() ?? Auth::id();

            $public_holiday->save();

            DB::commit();
            return $public_holiday;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    
}