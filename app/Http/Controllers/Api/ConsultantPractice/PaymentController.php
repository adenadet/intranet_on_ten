<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Payment;
use App\Models\Finance\AllBank;
use App\Services\ConsultantPractice\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ConsultantPracticeTrait;
 
    public function __construct(
        protected PaymentService $payment_service,
    ){}

    public function confirm(Request $request, int|string $id)
    {
        $payment = Payment::findOrFail($id);

        $payment = $this->payment_service->confirm($payment, $request->all());

        return response()->json([
            'payment' => $payment,
        ], is_string($payment) ? 404 : 200);
    }

    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);
        $query = $this->payment_service->cancel($payment);
        return response()->json(['payment' => $query,]);
    }

    public function index()
    {
        $query = $this->consultant_practice_payment_get_all($_GET['type'], $_GET, true, true);

        return response()->json(['payments' => $query,]);
    }

    public function initials()
    {
        return response()->json([
            'companies' => $this->consultant_practice_company_get_all('active', $_GET, false, false), 
        ]);
    }

    public function reverse(Request $request, string $id)
    {
        try{
            $payment = Payment::findOrFail($id);
            $query = $this->payment_service->reverse($payment, $request->all());
        }
        catch(Exception $e){
            $query = $e->getMessage();
        }

        return response()->json([
            'payment' => $query,
        ], is_string($query) ? 404 : 200);
    }

    public function show(string $id)
    {
        try{
            $query = $this->consultant_practice_payment_get_by($id, true);
        }
        catch(Exception $e){
            $query = $e->getMessage();
        }

        return response()->json([
            'payment' => $query,
        ], is_string($query) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'account_id' => 'numeric|required',
            'amount' => 'numeric|required',
            'company_id' => 'numeric|required',
            'date' => 'required|date', 
        ]);

        $payment = $this->payment_service->create($request->all());

        return response()->json([
            'payment' => $payment,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'account_id' => 'numeric|required',
            'amount' => 'numeric|required',
            'company_id' => 'numeric|required',
            'date' => 'required|date', 
        ]);

        $payment = Payment::findOrFail($id);

        $payment = $this->payment_service->update($payment, $request->all());
        
        return response()->json([
            'payment' => $payment,
        ]);
    }
}
