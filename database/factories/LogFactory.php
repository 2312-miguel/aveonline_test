<?php

namespace Database\Factories;

use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    protected $model = Log::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'endpoint' => $this->faker->url(), // Genera siempre un valor para 'endpoint'
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'ip_address' => $this->faker->ipv4(),   
        ];
    }
}
