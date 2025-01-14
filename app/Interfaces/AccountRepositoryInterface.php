<?php

namespace App\Interfaces;

use App\Models\Account;
use App\Models\User;

interface AccountRepositoryInterface
{
    /**
     * Obtiene la cuenta asociada a un usuario. 
     * (Asumiendo que cada usuario tiene una sola cuenta, si manejas múltiples, ajusta la lógica).
     *
     * @param User $user
     * @return Account|null
     */
    public function getUserAccount(User $user): ?Account;

    /**
     * Agrega saldo a la cuenta.
     *
     * @param Account $account
     * @param float $amount
     * @return Account
     */
    public function addBalance(Account $account, float $amount): Account;

    /**
     * Retira saldo de la cuenta (si hay saldo disponible).
     *
     * @param Account $account
     * @param float $amount
     * @return Account
     */
    public function withdrawBalance(Account $account, float $amount): Account;
}
