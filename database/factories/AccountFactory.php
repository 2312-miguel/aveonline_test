<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Genera automáticamente un usuario relacionado
            'balance' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
