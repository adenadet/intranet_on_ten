<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use Illuminate\Http\Request;
use App\Models\ConsultantPractice\Patient;
use App\Models\ConsultantPractice\Session;
use App\Models\ConsultantPractice\SessionConfirmation;
use App\Models\ConsultantPractice\SessionPayment;
use App\Models\ConsultantPractice\Specialty;
use App\Services\ConsultantPractice\SessionManagerService;

class SessionController extends Controller
{
    use ConsultantPracticeTrait;

    public function __construct(
        protected SessionManagerService $session_manager_service,
    ){}
    
    public function confirm_payment(Request $request)
    {
        $session = Session::find($request->input('session_id'));
        $session_manager = new SessionManagerService();
        
        $session = $request->input('decision') === 'reject' ? $session_manager->reject($session, $request->all()) : $session_manager->payment_confirm($session, $request->all());
    
        return response()->json([
            'sessions' => $session
        ], is_string($session) ? 500 : 200);
        
    }

    public function confirm_service(Request $request)
    {
        $session = Session::findOrFail($request->input('session_id'));
    
        $this->session_manager_service->service_confirm($session, $request->all());
        return response()->json(['sessions' => $session]);
    }

    public function destroy(string $id)
    {
        $session = $this->consultant_practice_session_deactivate($id);

        return response()->json([
            'sessions' => $session
        ], is_string($session) ? 500 : 200);
    }

    public function index()
    {
        $sessions = $this->consultant_practice_session_get_all($_GET['type'], $_GET, true, true);

        return response()->json([
            'sessions' => $sessions
        ]);
    }

    public function initials()
    {
        return response()->json([
            'consultants' => $this->consultant_practice_consultant_get_all('active', $_GET, true, false),
            'patients' => Patient::select('id', 'unique_id', 'name')->get(), 
            'specialties' => Specialty::with('consultants')->select('id', 'name')->get(),
        ]);
    }

    public function process($id){
        $session = Session::findOrFail($id);

        $invoice = $this->session_manager_service->process($session);

        return response()->json([
            'session' => $invoice,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric',
            'consultant_id' => 'required|exists:consultant_practice_consultants,id',
            'date' => 'required|date',
            'patient_id' => 'required|exists:consultant_practice_patients,id',
            'specialty_id' => 'nullable|exists:consultant_practice_specialties,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:consultant_practice_services,id',
            'services.*.price' => 'required|numeric',
            'services.*.adjusted_price' => 'required|numeric',
        ]);

        $session_manager = new SessionManagerService();
        $session = $session_manager->create($request);

        return response()->json([
            'sessions' => $session
        ], is_string($session) ? 500 : 201);
    }

    public function show(string $id)
    {
        $session = $this->consultant_practice_session_get_by($id, true);

        return response()->json([
            'session' => $session,
            'confirmation' => SessionConfirmation::where('session_id', $id)->with('creator')->first() ?? null,
            'payment' => SessionPayment::where('session_id', $id)->with('creator')->first() ?? null,
        ], is_string($session) ? 404 : 200);
    }

    public function update(Request $request, string $id)
    {
        $session = $this->consultant_practice_session_update($request, $id);

        return response()->json([
            'sessions' => $session
        ], is_string($session) ? 500 : 200);
    }
}
