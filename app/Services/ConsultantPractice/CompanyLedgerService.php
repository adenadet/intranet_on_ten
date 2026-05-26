<?php

namespace App\Services\ConsultantPractice;

use Illuminate\Support\Facades\DB;
use App\Models\ConsultantPractice\Company;
use App\Models\ConsultantPractice\CompanyLedger;
use Exception;
use Illuminate\Support\Facades\Auth;

class CompanyLedgerService
{
    public function credit(Company $company, float $amount, string $referenceType, int $referenceId, ?string $description = null, $date){
        return DB::transaction(function () use ($company, $amount, $referenceType, $referenceId, $description, $date) {
            $balance = $this->getBalance($company) + $amount;
            $company->balance = $balance;
            $company->save();
            return CompanyLedger::create([
                'date'            => $date ?? date('Y-m-d') ,
                'company_id'      => $company->id,
                'type'            => 'credit',
                'amount'          => $amount,
                'balance'         => $balance,
                'reference_type'  => $referenceType,
                'reference_id'    => $referenceId,
                'description'     => $description,
                'created_by'      => auth('api')->id() ?? Auth::id(),
                'updated_by'      => auth('api')->id() ?? Auth::id(),
            ]);
        });
    }

    public function debit(Company $company, float $amount, string $referenceType, int $referenceId, ?string $description = null, $date)
    {
        return DB::transaction(function () use ($company, $amount, $referenceType, $referenceId, $description, $date) {
            $balance = $this->getBalance($company) - $amount;
            //if ($balance < 0) {throw new Exception('Insufficient company balance');}
            $company->balance = $balance;
            $company->save();
            
            return CompanyLedger::create([
                'date'           => $date ?? date('Y-m-d'),
                'company_id'      => $company->id,
                'type'            => 'debit',
                'amount'          => $amount,
                'balance'         => $balance,
                'reference_type'  => $referenceType,
                'reference_id'    => $referenceId,
                'description'     => $description,
                'created_by'      => auth('api')->id() ?? Auth::id(),
                'updated_by'      => auth('api')->id() ?? Auth::id(),
            ]);
        });
    }

    public function getBalance(Company $company): float
    {
        return (float) CompanyLedger::where('company_id', $company->id)->latest('id')->value('balance') ?? 0;
    }
}