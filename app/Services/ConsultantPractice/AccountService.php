<?php

namespace App\Services\ConsultantPractice;
use App\Models\ConsultantPractice\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function create(array $data): Account
    {
        return DB::transaction(function () use ($data) {

            // Optional constraint: prevent duplicates
            $this->ensureUniqueAccount($data);

            return Account::create([
                'company_id'      => $data['company_id'],
                'bank_id'         => $data['bank_id'],
                'account_name'    => $data['account_name'],
                'account_number'  => $data['account_number'],
                'status'          => $data['status'] ?? Account::StatusActive,
                'created_by'      => auth('api')->id() ?? Auth::id(),
                'updated_by'      => auth('api')->id() ?? Auth::id(),
            ]);
        });
    }

    public function update(Account $account, array $data): Account
    {
        if ($account->deleted_at) {
            throw new \Exception('Cannot update a deleted account');
        }

        return DB::transaction(function () use ($account, $data) {

            // Optional: re-check uniqueness if key fields change
            if (isset($data['account_number']) || isset($data['bank_id']) || isset($data['company_id'])) {
                $this->ensureUniqueAccount($data, $account->id);
            }

            $account->update([
                'company_id'      => $data['company_id']     ?? $account->company_id,
                'bank_id'         => $data['bank_id']        ?? $account->bank_id,
                'account_name'    => $data['account_name']   ?? $account->account_name,
                'account_number'  => $data['account_number'] ?? $account->account_number,
                'status'          => $data['status']         ?? $account->status,
                'created_by'      => auth('api')->id() ?? Auth::id(),
                'updated_by'      => auth('api')->id() ?? Auth::id(),
            ]);

            return $account;
        });
    }

    public function activate(Account $account): Account
    {
        if ($account->status === Account::StatusActive) {
            return $account; // idempotent
        }

        return DB::transaction(function () use ($account) {

            $account->update([
                'status'     => Account::StatusActive,
                'updated_by' => auth()->id(),
            ]);

            return $account;
        });
    }

    public function deactivate(Account $account): Account
    {
        if ($account->status === Account::StatusInactive) {
            return $account; // idempotent
        }

        return DB::transaction(function () use ($account) {

            $account->update([
                'status'     => Account::StatusInactive,
                'updated_by' => auth()->id(),
            ]);

            return $account;
        });
    }

    protected function ensureUniqueAccount(array $data, ?int $ignoreId = null): void
    {
        $query = Account::where('company_id', $data['company_id'])
            ->where('bank_id', $data['bank_id'])
            ->where('account_number', $data['account_number']);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw new \Exception('Account already exists for this company and bank');
        }
    }
}