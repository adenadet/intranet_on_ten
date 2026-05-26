<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Models\ConsultantPractice\Company;
use App\Models\ConsultantPractice\Account;
use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Specialty;
use App\Models\Finance\AllBank;
use App\Services\ConsultantPractice\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function destroy(string $id)
    {
        $account = Account::find($id);
        $account_manager = new AccountService();

        $account = $account->status == Account::StatusActive ? $account_manager->deactivate($account) : $account_manager->activate($account);

        return response()->json(['account' => $account]);
    }

    public function index()
    {
        $accounts = Account::query()->with(['company', 'specialty'])->orderBy('first_name', 'ASC')->paginate(20);
        return response()->json([
            'accounts' => $accounts
        ]);
    }

    public function initials(){
        return response()->json([
            'banks' => AllBank::query()->orderBy('bank_name', 'ASC')->get(['id', 'bank_name']),
            'companies' => Company::query()->orderBy('name', 'ASC')->get(['id', 'name']),
        ]);
    }

    public function show(string $id)
    {
        $account = Account::with(['bank','company', 'creator', 'deleter', 'updater'])->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        return response()->json([
            'account' => $account,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'company_id' => 'required|numeric|exists:consultant_practice_companies,id',
            'bank_id' => 'required|numeric|exists:all_banks,id',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:25',
        ]);
        $account_manager = new AccountService();
        $account = $account_manager->create($request->all());

        return response()->json([
            'account' => $account
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'company_id' => 'required|numeric|exists:consultant_practice_companies,id',
            'bank_id' => 'required|numeric|exists:finance_all_banks,id',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:25',
        ]);
        $account = Account::findOrFail($id);
        $account_manager = new AccountService();
        $account = $account_manager->update($account, $request->all());

        return response()->json([
            'account' => $account
        ]);
    }
}