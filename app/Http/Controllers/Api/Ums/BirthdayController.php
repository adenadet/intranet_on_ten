<?php

namespace App\Http\Controllers\Api\Ums;

use App\Http\Controllers\Controller;
use App\Models\Hrms\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
class BirthdayController extends Controller
{
    public function latest(){
        $employees = Employee::where('employment_status', 1)->pluck('user_id');
        return response()->json([
            'birthdays'     => User::birthDayBetween(Carbon::now(), Carbon::now()->addWeek())->limit(8)
            ->whereIn('id', $employees)  
            ->orderByRaw("DATE_FORMAT(dob, '%m-%d') ASC")  
            ->get(),
        ]);
    }

    public function index()
    {
        $employees = Employee::where('employment_status', 1)->pluck('user_id');
        return response()->json([
            'birthdays'     => User::birthDayBetween(Carbon::now(), Carbon::now()->addWeek())->limit(8)
            ->whereIn('id', $employees)  
            ->orderByRaw("DATE_FORMAT(dob, '%m-%d') ASC")  
            ->get(),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
