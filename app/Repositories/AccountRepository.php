<?php

namespace App\Repositories;

use App\Interfaces\AccountRepositoryInterface;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * Get the account associated with a user.
     *
     * @param User $user
     * @return Account|null
     */
    public function getUserAccount(User $user): ?Account
    {
        // Assuming one user has one account. Adjust if different.
        return Account::where('user_id', $user->id)->first();
    }

    /**
     * Add balance to an account.
     *
     * @param Account $account
     * @param float $amount
     * @return Account
     */
    public function addBalance(Account $account, float $amount): Account
    {
        // Use a DB transaction to avoid race conditions
        DB::transaction(function () use ($account, $amount) {
            $account->balance += $amount;
            $account->save();
        });

        return $account;
    }

    /**
     * Withdraw balance from an account.
     *
     * @param Account $account
     * @param float $amount
     * @return Account
     * @throws \Exception
     */
    public function withdrawBalance(Account $account, float $amount): Account
    {
        DB::transaction(function () use ($account, $amount) {
            if ($account->balance >= $amount) {
                $account->balance -= $amount;
                $account->save();
            } else {
                // Throw an exception if there is insufficient balance
                throw new \Exception('Insufficient balance');
            }
        });

        return $account;
    }
}
