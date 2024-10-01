<?php
namespace App\Http\Traits\Ums;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\FileManagerTrait;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;

use App\Models\EMR\Patient;
use App\Models\HRMS\Employee;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


trait UserTrait{
    use FileTrait, FileManagerTrait;

    public function user_create_new_staff($request){
        $user = $this->user_create_new_user($request, null);
        $staff = Employee::create([
            'user_id' => $user->id,
            'supervisor_id' => $request->supervisor_id,
            'reports_to' => $request->reports_to,
            'department_id' => $user->department_id,
            'unit_id' => $request->unit_id ?? NULL,
            'company_id' => $request->company_id ?? NULL,
            'salary_template' => $request->salary_template ?? NULL,
            'joined_at' => $user->joined_at,
            'left_at' => $request->input('left_at') ?? NULL,
            'designation_id' => $request->input('designation_id') ?? NULL,
            'created_by' => auth('api')->id(),
            'updated_at' => auth('api')->id(),
        ]);
    }

    public function user_create_new_user($request, $image_url){
        $image_url = $this->file_upload_to_location($request->input('image'), 'image', 'img/profile', null);
        $image_url = (!is_null($request->input('image'))) ? $this->file_upload($request->input('image'), 'image', 'img/profile/', null) : 'default.png';
        $user = User::create([
            'email' => $request['email'],
            'first_name' => $request['first_name'],
            'middle_name' => $request['middle_name'],
            'last_name' => $request['last_name'],
            'street' => $request['street'],
            'street2' => $request['street2'],
            'city' => $request['city'],
            'state_id' => $request['state_id'],
            'area_id' => $request['area_id'],
            'personal_email' => $request['personal_email'],
            'phone' => $request['phone'],
            'alt_phone' => $request['alt_phone'],
            'branch_id' => $request['branch_id'],
            'department_id' => $request['department_id'],
            'sex' => $request['sex'],
            'dob' => $request['dob'],
            'image' => $image_url,
            'updated_at' => date('Y-m-d H:i:s'),
            'joined_at' => $request['joined_at'],
            'unique_id' => $request['unique_id'],
            'password' => bcrypt('asdfasdf'),
        ]);
        return $user;
    }

    public function user_staff_deactivate_by_id($id){
        DB::beginTransaction();

        try{
            $staff = User::where('id', '=', $id)->first();
            $staff->status = 'inactive';
            $staff->deleted_by = auth('api')->id() ?? Auth::id();
            $staff->deleted_at = date('Y-m-d H:i:s');
            $staff->save();
            DB::commit();
            return $staff;
        }
        catch (Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function user_get_all(){
        return User::where('status', '=', 'active')->orderBy('first_name', 'ASC')->with(['area', 'branch', 'department', 'roles', 'state'])->paginate(52);
    }

    public function user_staffs_get_all($type, $specific, $detailed, $paginated, $page){
        //$staffs = Staff::pluck('user_id');
        switch ($type){
            case 'active':
                $query = Employee::where('is_active', '=', 1)->orderBy('employee_id', 'ASC');
            break;
            case 'all':
                $query = Employee::orderBy('employee_id', 'ASC')->has('user');
            break;
            default:
                $query = Employee::orderBy('employee_id', 'ASC');
            break;
        }

        $query = $detailed ? $query->with(['department', 'designation', 'user.area', 'user.branch', 'user.department', 'user.roles', 'user.state', 'supervisor.user',  'line_manager.user']) : $query->select('id', 'user_id')->with('user');
        
        $query = $paginated ? $query->paginate(52) : $query->get();
        
        return $query;
    }

    public function user_staffs_get_by_id($id){
        return Employee::where('id', '=', $id)->with(['user.area', 'user.branch', 'user.department', 'user.roles', 'user.state', 'supervisor.user',  'line_manager.user'])->first();
    }

    public function user_update_user($request, $id){
        $user = User::where('id', '=', $id)->first();
        //check if the image has changed
        if (!is_null($request->input('image'))){
            if ($request->image == $user->image){$image_url = $request->image;}
            else{$image_url = $this->file_upload_to_location($request->input('image'), 'image', 'img/profile', null);}
        }
        else{$image_url = 'default.png';}
        //$image_url = ((!(is_null($request->input('image')))) && ($request->input('image') != $user->image))? $this->file_upload($request->input('image'), 'image', 'uploads/profile', $id): $user->image;
        
        $user->email = $request['email'];
        $user->first_name = $request['first_name'];
        $user->middle_name = $request['middle_name'];
        $user->last_name = $request['last_name'];
        $user->street = $request['street'];
        $user->street2 = $request['street2'];
        $user->city = $request['city'];
        $user->state_id = $request['state_id'];
        $user->area_id = $request['area_id'];
        $user->personal_email = $request['personal_email'];
        $user->phone = $request['phone'];
        $user->alt_phone = $request['alt_phone'];
        $user->branch_id = $request['branch_id'];
        $user->department_id = $request['department_id'];
        $user->sex = $request['sex'];
        $user->dob = $request['dob'];
        $user->image = $image_url;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->joined_at = $request->input('joined_at');
        $user->dob = $request->input('dob');
        $user->unique_id = $request->input('unique_id');
            
        $user->save();

        return $user;
    }
}