<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(), // Genera automáticamente una cuenta
            'transaction_number' => $this->faker->unique()->uuid(), // Genera un número único
            'amount' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}
