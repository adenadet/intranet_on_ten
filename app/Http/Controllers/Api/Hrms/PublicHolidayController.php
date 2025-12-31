<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\BasicTrait;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
    use BasicTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'public_holidays' => $this->hrms_basic_public_holiday_get_all('all', $_GET, true, true),    
        ]);
    }


    public function store(Request $request)
    {
        $public_holiday = $this->hrms_basic_public_holiday_create($request->all());
        return response()->json([
            'public_holiday'   => $public_holiday,
        ], is_string($public_holiday) ? 500 : 200);
    }

    
    public function show(string $id)
    {
        $public_holiday = $this->hrms_basic_public_holiday_get_by($id);
        return response()->json([
            'public_holiday'   => $public_holiday,
        ], is_string($public_holiday) ? 404 : 200);
    }

    public function update(Request $request, string $id)
    {
        $public_holiday = $this->hrms_basic_public_holiday_update($request,$id);
        return response()->json([
            'public_holiday'   => $public_holiday,
        ], is_string($public_holiday) ? 404 : 200);
    }

    public function destroy(string $id)
    {
        $public_holiday = $this->hrms_basic_public_holiday_deactivate($id);
        return response()->json([
            'public_holiday'   => $public_holiday,
        ], is_string($public_holiday) ? 404 : 200);
    }
}
