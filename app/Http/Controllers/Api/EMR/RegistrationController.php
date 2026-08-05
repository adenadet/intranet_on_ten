<?php
namespace App\Http\Controllers\Api\EMR;

use App\Http\Controllers\Controller;
use App\Http\Traits\EService\AppointmentTrait;
use App\Mail\RegistrationMail as RegMail;
use App\Mail\RescheduleMail as ResMail;
use App\Models\EMR\Appointment;
use App\Models\EMR\Schedule;
use App\Models\EMR\Service;
use App\Models\Country;
use App\Models\EMR\Payment;
use App\Models\Hrms\PublicHoliday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    use AppointmentTrait;
    public function index(){
        $daysToSearch = 30;
        $public_holidays = PublicHoliday::where('date', '>=', date('Y-m-d'))->where('date', '<=', date('Y-m-d', strtotime('+'.$daysToSearch.' days')))->pluck('date')->toArray();

        if (!empty($public_holidays)) {
            $phPlaceholders = implode(',', array_fill(0, count($public_holidays), '?'));
            $phClause = "AND d.date NOT IN ($phPlaceholders)";
            $bindings = array_merge([1, 1, $daysToSearch], $public_holidays);
        } else {
            $phClause = ""; 
            $bindings = [$daysToSearch, 1];
        }

        $sql = "SELECT d.date, s.schedule FROM(
            SELECT DATE_ADD(CURDATE(), INTERVAL seq.day_number DAY) AS date, seq.day_number FROM
            (
                SELECT (u.n + t.n * 10) AS day_number
                FROM (
                        SELECT 0 AS n UNION ALL
                        SELECT 1 UNION ALL
                        SELECT 2 UNION ALL
                        SELECT 3 UNION ALL
                        SELECT 4 UNION ALL
                        SELECT 5 UNION ALL
                        SELECT 6 UNION ALL
                        SELECT 7 UNION ALL
                        SELECT 8 UNION ALL
                        SELECT 9
                    ) u
                CROSS JOIN
                    (
                        SELECT 0 AS n UNION ALL
                        SELECT 1 UNION ALL
                        SELECT 2 UNION ALL
                        SELECT 3 UNION ALL
                        SELECT 4 UNION ALL
                        SELECT 5 UNION ALL
                        SELECT 6 UNION ALL
                        SELECT 7 UNION ALL
                        SELECT 8 UNION ALL
                        SELECT 9
                    ) t
                ) seq
                WHERE seq.day_number BETWEEN 1 AND ?
            ) d
        
            INNER JOIN emr_service_schedules s ON s.service_id = ?
            LEFT JOIN emr_appointments a
                ON a.service_id = s.service_id
                AND a.date = d.date
                AND a.schedule = s.schedule
                AND a.deleted_at IS NULL
                AND a.status <> 'cancelled'
            WHERE DAYOFWEEK(d.date) NOT IN (1,7) {$phClause} AND a.id IS NULL
            ORDER BY d.date, s.schedule
            LIMIT 5";

        $uk_tb_slots = DB::select($sql, $bindings);

        return response()->json([
            'nations'  => Country::orderBy('name', 'ASC')->get(),   
            'services' => Service::orderBy('name', 'ASC')->get(),
            'uk_tb_slots' => $uk_tb_slots,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'last_name' => 'required',
            'first_name' => 'required',
            'email' => 'required',
            'service_id' => 'required',
            'date' => 'required | date',
            'schedule' => 'sometimes',
        ]);

        $appointment = $this->appointment_create($request);

        return response()->json([
            'appointment' => $appointment
        ]);
    }

    public function show($id)
    {
        //echo $id;
        return response()->json([
            'appointment' => Appointment::where('transaction_id', '=', $id)->where('status', '=', 1)->with(['service', 'patient', 'payment'])->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::where('id', '=', $id)->with(['service', 'patient', 'payment'])->first();
        $payment = Payment::create([
            'service_id' => $appointment->service_id, 
            'patient_id' => $appointment->patient_id, 
            'appointment_id' => $appointment->id,
            'amount' => $appointment->amount, 
            'employee_id' => 0,
            'channel' => $request->input('payment_channel') ?? "Paystack", 
            'details' => $request->input('payment_transaction').' | '.$request->input('payment_reference'),    
        ]);

        $appointment->status = 1;
        $appointment->transaction_id = "SNH-".$appointment->id."-".$payment->id."-".$appointment->patient_id;
        $appointment->save();

        $consultation = Appointment::where('id', '=', $appointment->id)->with(['service', 'patient', 'payment'])->first();
        $patient = $appointment->patient;

        $dayOfWeek = date('w', strtotime($request->input('date')));
        if ($dayOfWeek != 0 || $dayOfWeek != 6) {
            Mail::to($patient->email)->send(new RegMail($consultation));
            return response()->json([
                'appointment' => $appointment,
            ]);
        }

    }

    public function destroy($id)
    {
        //
    }

    public function resend(int|string $id)
    {
        $appointment = Appointment::where('id', '=', $id)->first();
        $payment = Payment::where('appointment_id', '=', $id)->first();

        if ((is_null($appointment)) || (is_null($payment))){
            return response()->json([
                'message' => 'Appointment has not been paid or does not exist',
                'status' => 'error',
            ]);
        }

        $appointment->transaction_id = "SNH-".$appointment->id."-".$payment->id."-".$appointment->patient_id;
        $appointment->save();

        $consultation = Appointment::where('id', '=', $appointment->id)->with(['service', 'patient', 'payment'])->first();
        if(is_null($consultation->patient->email)){
            return response()->json([
                'message' => 'Patient does not have a valid email address',
                'status' => 'error',
            ]);
        }
        Mail::to($consultation->patient->email)->send(new RegMail($consultation));
        return response()->json([
            'message' => 'Mail has been resent successfully',
            'status' => 'success',
        ]);
    }
    public function schedules()
    {
      	$date = $_GET['date'];
      	$public_holidays = PublicHoliday::where('status', '=', 1)->pluck('date')->toArray(); 
        if (in_array($date, $public_holidays)){
        	$schedules = [];
        }
        else if (($date = $_GET['date']) && ($service_id = $_GET['service_id'])){
            $taken = Appointment::select('schedule')->where([['date', '=', $date], ['status', '>=', '1']])->get();
            $schedules = Schedule::select('schedule')->where('service_id', '=', $service_id)->whereNotIn('schedule', $taken)->get();
        }
        else{
            $schedules = Schedule::select('schedule')->where('service_id', '=', $service_id)->get();
        }
        
        return response()->json(['schedules' => $schedules,]);
    }
}
