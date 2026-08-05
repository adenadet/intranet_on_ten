<?php
namespace App\Http\Traits\ConsultantPractice;

use App\Models\ConsultantPractice\Account;
use App\Models\ConsultantPractice\Company;
use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\ConsultantService;
use App\Models\ConsultantPractice\Patient;
use App\Models\ConsultantPractice\Payment;
use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Session;
use App\Models\ConsultantPractice\Specialty;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ConsultantPracticeTrait{
    use SettingsTrait;

    public function consultant_practice_company_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = Company::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
                $query = $query->withTrashed();
            break;
            case 'front':
                $query = $query->where('status', '=', Company::StatusActive);
            break;
            case 'medical':
                $query = $query->where('status', '=', Company::StatusActive);
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = trim($specific['query']);
                $query = $query->where('name', 'like', "%$search%");
            }
        }

        $query = $detailed ? $query->with(['consultants.specialty', 'accounts.bank']) : $query->select('id', 'name',)->with(['accounts.bank']);
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_company_get_by(int|string $id, bool $detailed){
        try{
            $query = Company::where('id', '=', $id);
            $query = $detailed ? $query->with(['consultants.specialty', 'accounts.bank', 'ledgers.referenceable']) : $query->select('id', 'name',)->with(['accounts.bank']);
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_consultant_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = Consultant::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
                $query = $query->withTrashed();
            break;
            case 'front':
                $query = $query->where('status', '=', Consultant::StatusActive);
            break;
            case 'medical':
                $query = $query->where('status', '=', Consultant::StatusActive);
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = trim($specific['query']);
                $query = $query->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
            }
        }

        $query = $detailed ? $query->with(['creator', 'company', 'specialty', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
        $query->orderBy('first_name', 'ASC');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_consultant_get_by(int|string $id, bool $detailed){
        try{
            $query = Consultant::where('id', '=', $id);
            $query = $detailed ? $query->with(['company', 'creator', 'specialty', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title')->with(['company']);
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_consultant_service_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = ConsultantService::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
                $query = $query->withTrashed();
            break;
            case 'front':
                $query = $query->where('status', '=', ConsultantService::StatusActive);
            break;
            case 'medical':
                $query = $query->where('status', '=', ConsultantService::StatusActive);
            break;
        }

        if(is_array($specific)){
            if(!empty($specific['consultant_id'])){
                $query = $query->where('consultant_id', $specific['consultant_id']);
            } 
            if(!empty($specific['service_id'])){
                $query = $query->where('service_id', $specific['service_id']);
            }
        }

        $query = $detailed ? $query->with(['consultant', 'creator', 'service', 'updater']) : $query->select('id', 'service_id', 'consultant_id')->with(['service']);
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_consultant_service_get_by(int|string $id, bool $detailed){
        try{
            $query = Consultant::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['consultant', 'creator', 'payments', 'pricelists', 'service', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function consultant_practice_patient_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = Patient::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
                $query = $query->withTrashed();
            break;
            case 'front':
                $query = $query->where('status', '=', Patient::StatusActive);
            break;
            case 'medical':
                $query = $query->where('status', '=', Patient::StatusActive);
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = trim($specific['query']);
                
                $query = $query->where('name', 'LIKE', "%$search%");
            }
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'unique_id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated  ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function consultant_practice_patient_get_by($type, string|int $id, bool $detailed){
        try{
            $query = Patient::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'unique_id', 'name');
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_payment_get_all(string $type, array|null $specific, bool $detailed, bool $paginated){
        $query = Payment::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
            break;
            case 'front':
                $query = $query->where('status', '=', Payment::StatusConfirmed);
            break;
            case 'medical':
                $query = $query->where('status', '=', Payment::StatusConfirmed);
            break;
        }

        if (is_array($specific)){

        }

        $query = $detailed ? $query->with(['creator', 'account.bank', 'company', 'confirmer', 'reverser', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
        $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_payment_get_by(int|string $id, bool $detailed){
        try{
            $query = Payment::where('id', '=', $id);
            $query = $detailed ? $query->with(['account.bank', 'confirmer', 'company', 'creator', 'reverser', 'updater']) : $query->select('id', 'date', 'amount', 'company_id', 'status')->with(['company']);
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_service_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = Service::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
                $query = $query->withTrashed();
            break;
            case 'front':
                $query = $query->where('status', '=', Service::StatusActive);
            break;
            case 'medical':
                $query = $query->where('status', '=', Service::StatusActive);
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = trim($specific['query']);
                $query = $query->where('name', 'LIKE', "%$search%");
            }
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated  ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function consultant_practice_service_get_by($type, int|string $id, bool $detailed){
        try{
            $query = Service::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_session_deactivate(int|string $id){
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
    
    public function consultant_practice_session_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = Session::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'front':
                $query = $query->where('payment_status', '!=', Session::PaymentStatusCancelled);
            break;
            case 'finance':
                $query = $query->where('payment_status', '=', Session::PaymentStatusPaid)->where('service_status', '=', Session::ServiceStatusCompleted);
            break;
            case 'medical':
                $query = $query->where('payment_status', '=', Session::PaymentStatusPaid);
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

        $query = $detailed ? $query->with(['consultant.company', 'consultant.specialty', 'patient', 'creator', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
        $query = $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_session_get_by(int|string $id, bool $detailed){
        try{
            $query = Session::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['consultant.company', 'consultant.specialty', 'creator', 'patient', 'session_confirm', 'session_items.service', 'session_payment', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
            
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_specialty_get_all(string $type, array $specific, bool $detailed, bool $paginated){
        $query = Specialty::query();

        switch($type){
            case 'admin':
                $query = $query->withTrashed();
            break;
            case 'finance':
                $query = $query->where('status', '=', Specialty::StatusActive);
            break;
            case 'front':
                $query = $query->where('status', '=', Specialty::StatusActive);
            break;
            case 'medical':
                $query = $query->where('status', '=', Specialty::StatusActive);
            break;
        }

        if (is_array($specific)){

        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_specialty_get_by(int|string $id, bool $detailed){
        try{
            $query = Payment::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}