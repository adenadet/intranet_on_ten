<?php
namespace App\Services\Ums;
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
use App\Models\Hrms\Employee;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class UserService{

    public function create(array $data){
        return DB::transaction(function () use ($data) {
            //$image_url = (!is_null($data['image'])) ? $this->file_upload($data['image'], 'image', 'img/profile/', null) : 'default.png';
            $image_url = 'default.png';
            $user = User::create([
                'email' => $data['email'] ?? null,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'street' => $data['street'] ?? null,
                'street2' => $data['street2'] ?? null,
                'city' => $data['city'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'area_id' => $data['area_id'] ?? null,
                'personal_email' => $data['personal_email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'alt_phone' => $data['alt_phone'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'sex' => $data['sex'] ?? null,
                'dob' => $data['dob'] ?? null,
                'image' => $image_url ?? null,
                'joined_at' => $data['joined_at'] ??  $data['date_of_joining'] ?? date('Y-m-d'),
                'unique_id' => $data['username'] ?? $data['unique_id'] ?? null,
                'password' => password_hash('asdfasdf', PASSWORD_DEFAULT),
            ]);
            return $user;
        });
    }

    public function deactivate(int|string $id){
        return DB::transaction(function () use ($id) {
        
            $staff = User::where('id', '=', $id)->first();
            $staff->status = 'inactive';
            $staff->deleted_by = auth('api')->id() ?? Auth::id();
            $staff->deleted_at = date('Y-m-d H:i:s');
            $staff->save();
            
            return $staff;
        });
    }

    public function update(array $request, int|string $id){
        return DB::transaction(function () use ($request, $id) {
            $user = User::where('id', '=', $id)->first();
            //check if the image has changed
            if (!is_null($request['image'])){
                if ($request['image'] == $user->image){$image_url = $request['image'];}
                //else{$image_url = $this->file_upload_to_location($request->input('image'), 'image', 'img/profile', null);}
            }
            else{$image_url = 'default.png';}
            //$image_url = ((!(is_null($request->input('image')))) && ($request->input('image') != $user->image))? $this->file_upload($request->input('image'), 'image', 'uploads/profile', $id): $user->image;
            
            $user->update([
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
            ]); 
            return $user;
        });
    }

}