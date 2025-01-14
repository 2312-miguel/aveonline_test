<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Str;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Create a new transaction.
     *
     * @param Account $account
     * @param array $data
     * @return Transaction
     */
    public function createTransaction(Account $account, array $data): Transaction
    {
        // Generate a unique transaction number if not provided in $data
        $transactionNumber = $data['transaction_number'] ?? 'TX-' . Str::random(8);

        return Transaction::create([
            'transaction_number' => $transactionNumber,
            'account_id'         => $account->id,
            'amount'             => $data['amount'],
            'type'               => $data['type'],
        ]);
    }

    /**
     * Find a transaction by its transaction number.
     *
     * @param string $transactionNumber
     * @return Transaction|null
     */
    public function findByTransactionNumber(string $transactionNumber): ?Transaction
    {
        return Transaction::where('transaction_number', $transactionNumber)->first();
    }
}
