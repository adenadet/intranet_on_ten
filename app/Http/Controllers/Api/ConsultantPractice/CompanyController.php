<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Company;
use App\Models\ConsultantPractice\CompanyLedger;
use App\Services\ConsultantPractice\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ConsultantPracticeTrait;
    public function destroy(string $id)
    {
        $company = Company::find($id);

        if (!$company) {return response()->json(['message' => 'Company not found.'], 404);}
        $company_manager = new CompanyService();
        $company->status == Company::StatusInactive ? $company_manager->reactivate($id): $company_manager->deactivate($id);

        return response()->json([
            'message' => 'Company deactivated/reactivated successfully.'
        ]);
    }

    public function ledgers(){
        $query = CompanyLedger::query()->with(['creator', 'company'])->orderBy('created_at', 'DESC');

        if (!empty($_GET['company_id'])){
            $query = $query->where('company_id', '=', $_GET['company_id']);
        }
        if (!empty($_GET['end_date'])){
            $query = $query->where('date', '<=', $_GET['end_date']);
        }
        if (!empty($_GET['start_date'])){
            $query = $query->where('date', '>=', $_GET['start_date']);
        }
        if (!empty($_GET['type'])){
            $query = $query->where('type', '=', $_GET['type']);
        }
        
        return response()->json([
            'ledgers' => $query->paginate(30)
        ]);
    }

    public function index()
    {
        $query = $this->consultant_practice_company_get_all($_GET['type'] ?? 'front', $_GET, true, true);

        return response()->json([
            'companies' =>$query
        ]);
    }

    public function show(string $id)
    {
        $company = $this->consultant_practice_company_get_by($id, true);

        return response()->json([
            'company' => $company,
            'ledgers' => CompanyLedger::where('company_id', $id)->with(['creator', 'company'])->orderBy('date', 'DESC')->limit(10)->get()
        ], is_string($company) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $company_manager = new CompanyService();

        $company = $company_manager->create($request->all());

        return response()->json([
            'company' => $company
        ]);
    }

    public function update(Request $request, string $id)
    {
        $company_manager = new CompanyService();

        $company = $company_manager->update($request->all(), $id);

        return response()->json([
            'company' => $company
        ]);
    }
}
