<?php
namespace App\Http\Traits\ConsultantPractice;

use App\Models\ConsultantPractice\Account;
use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\Session;
use App\Models\ConsultantPractice\Specialty;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


trait ConsultantTrait{

    use SettingsTrait;
    public function consultant_practice_consultant_create($data){
        DB::beginTransaction();

        try{
            $query = Consultant::create([
                'unique_id' => $this->consultant_practice_unique_id('consultant', 10),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'title' => $data['title'],
                'company_name' => $data['company_name'],
                'specialty_id' => $data['specialty_id'],
                'status' => Consultant::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function consultant_practice_consultant_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Consultant::where('id', '=', $id)->orWhere('unique_id', '=', $id)->withTrashed()->firstOrFail();

            if ($query->status == Consultant::StatusActive){
                $query->status = Consultant::StatusInactive;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = Consultant::StatusActive;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function consultant_practice_consultant_get_all($type, $specific, $detailed, $paginated){
        $query = Consultant::query();

        switch($type){
            //case ''
        }

        if (is_array($specific)){

        }

        $query = $detailed ? $query->with(['creator', 'payments', 'pricelists', 'specialty', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_consultant_get_by($id, $detailed){
        try{
            $query = Consultant::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'payments', 'pricelists', 'specialty', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_consultant_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Consultant::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            $query->first_name = $data['first_name'] ?? $query->first_name;
            $query->last_name = $data['last_name'] ?? $query->last_name;
            $query->title = $data['title'] ?? $query->title;
            $query->company_name = $data['company_name'] ?? $query->company_name;
            $query->specialty_id = $data['specialty_id'] ?? $query->specialty_id;
            $query->status = $data['status'] ?? Consultant::StatusActive;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }


    /*
    ----------------------------------------------------------------------------
    Specialty functions 
    -----------------------------------------------------------------------------
    */
    public function consultant_practice_specialty_create($data){
        DB::beginTransaction();

        try{
            $query = Specialty::create([
                'unique_id' => $this->consultant_practice_unique_id('consultant', 10),
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => Specialty::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function consultant_practice_specialty_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Specialty::where('id', '=', $id)->orWhere('unique_id', '=', $id)->withTrashed()->firstOrFail();

            if ($query->status == Specialty::StatusActive){
                $query->status = Specialty::StatusInactive;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = Specialty::StatusActive;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function consultant_practice_specialty_get_all($type, $specific, $detailed, $paginated){
        $query = Specialty::query();

        switch($type){
            //case ''
        }

        if (is_array($specific)){

        }

        $query = $detailed ? $query->with(['creator', 'consultants', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_specialty_get_by($id, $detailed){
        try{
            $query = Specialty::where('id', '=', $id)->orWhere('unique_id', '=', $id);
        $query = $detailed ? $query->with(['creator', 'consultants', 'updater']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_specialty_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Specialty::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? Specialty::StatusActive;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
}