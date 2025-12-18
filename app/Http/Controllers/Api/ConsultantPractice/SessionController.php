<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantTrait;
use Illuminate\Http\Request;
use App\Http\Traits\ConsultantPractice\SessionTrait;
use App\Models\ConsultantPractice\Patient;
use App\Models\ConsultantPractice\Specialty;

class SessionController extends Controller
{
    use ConsultantTrait, SessionTrait;
    
    public function confirm(Request $request, string $id)
    {
        $session = $this->consultant_practice_session_confirm($_GET['type'], $request, $id);

        return response()->json([
            'sessions' => $session
        ], is_string($session) ? 500 : 200);
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
            'consultants' => $this->consultant_practice_consultant_get_all('active', null, true, false),
            'patients' => Patient::select('id', 'unique_id', 'name')->get(), 
            'specialties' => Specialty::with('consultants')->select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $session = $this->consultant_practice_session_create($request);

        return response()->json([
            'sessions' => $session
        ], is_string($session) ? 500 : 201);
    }

    public function show(string $id)
    {
        $session = $this->consultant_practice_session_get_by($id, true);

        return response()->json([
            'sessions' => $session
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
