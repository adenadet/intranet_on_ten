<?php
namespace App\Http\Traits\ConsultantPractice;

use App\Models\ConsultantPractice\Account;
use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\Session;
use App\Models\ConsultantPractice\SessionConfirmation;
use App\Models\ConsultantPractice\Specialty;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SessionTrait{
    use SettingsTrait;

    public function consultant_practice_session_confirm($type, $data, $id){
        DB::beginTransaction();
            
        try{
            $session = Session::findOrFail($id);
            $prev_session_confirm = SessionConfirmation::where('session_id', '=', $id)->where('type', '!=', $type)->get()->count();
            
            $session_confirmation = SessionConfirmation::create([
                'type'          => $type,
                'session_id'    => $id,
                'details'       => $data['details'],
                'created_by'    => Auth::id() ?? auth('api')->id(), 
                'updated_by'    => Auth::id() ?? auth('api')->id(), 
            ]);

            $session->status = $prev_session_confirm == 0 ? Session::StatusProcessing : Session::StatusAwaitingPayment;
            $session->updated_by = Auth::id() ?? auth('api')->id();
            $session->save();

            DB::commit();
            return $session_confirmation;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        } 
    }

    public function consultant_practice_session_create($data){
        DB::beginTransaction();

        try{
            $query = Session::create([
                'unique_id' => $this->consultant_practice_unique_id('session', 10),
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

    public function consultant_practice_session_deactivate($id){
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

    public function consultant_practice_session_get_all($type, $specific, $detailed, $paginated){
        $query = Session::query();

        switch($type){
            case 'awaiting':
                $query = $query->where('status', '=', Session::StatusAwaitingPayment);
            break;
            case 'created':
                $query = $query->where('status', '=', Session::StatusCreated);
            break;
            case 'deleted':
                $query = $query->where('status', '=', Session::StatusRejected);
            break;
            case 'processing':
                $query = $query->where('status', '=', Session::StatusProcessing);
            break;
            case 'paid':
                $query = $query->where('status', '=', Session::StatusPaid);
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['start_date'])){
                $query = $query->whereDate('date', '>=', $specific['start_date']);
            }

            if(!empty($specific['end_date'])){
                $query = $query->whereDate('date', '<=', $specific['end_date']);
            }

            if(!empty($specific['query'])){
                $search = trim($specific['query']);

                $query->where(function ($q) use ($search) {

                    // Specialty name
                    $q->whereHas('specialty', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    })

                    // Patient name
                    ->orWhereHas('patient', function ($p) use ($search) {
                        $p->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                    })

                    // Consultant name
                    ->orWhereHas('consultant', function ($c) use ($search) {
                        $c->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                    });
                });
            }
        }

        $query = $detailed ? $query->with(['creator', 'payments', 'pricelists', 'specialty', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_session_get_by($id, $detailed){
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

    public function consultant_practice_session_update($data, $id){
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

}