<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantTrait;
use App\Http\Traits\ConsultantPractice\SessionTrait;
use App\Models\ConsultantPractice\Patient;
use App\Models\ConsultantPractice\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ConsultantTrait, SessionTrait;
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        return response()->json([
            'consultants' => $this->consultant_practice_consultant_get_all('active', null, true, true),
            'patients' => Patient::paginate(30),
            'payments' => Payment::paginate(30),
            'sessions' => $this->consultant_practice_session_get_all('active', null, true, true),
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
