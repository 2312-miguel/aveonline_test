<?php

namespace App\Interfaces;

use App\Models\Transaction;
use App\Models\Account;

interface TransactionRepositoryInterface
{
    /**
     * Crea una nueva transacción.
     *
     * @param Account $account
     * @param array $data
     * @return Transaction
     */
    public function createTransaction(Account $account, array $data): Transaction;

    /**
     * Encuentra una transacción por su número.
     *
     * @param string $transactionNumber
     * @return Transaction|null
     */
    public function findByTransactionNumber(string $transactionNumber): ?Transaction;
}
