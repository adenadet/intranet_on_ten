<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ConsultantPracticeTrait;
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        return response()->json([
            'companies' => $this->consultant_practice_company_get_all($_GET['type'] ?? 'front', $_GET, true, true),
            'consultants' => $this->consultant_practice_consultant_get_all($_GET['type'] ?? 'front', $_GET, true, true),
            'patients' => $this->consultant_practice_patient_get_all($_GET['type'] ?? 'front', $_GET, true, true),
            'payments' => $this->consultant_practice_payment_get_all($_GET['type'] ?? 'front', $_GET, true, true),
            'services' => $this->consultant_practice_service_get_all($_GET['type'] ?? 'front', $_GET, true, true),
            'sessions' => $this->consultant_practice_session_get_all($_GET['type'] ?? 'front', $_GET, true, true),
        ]);
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
