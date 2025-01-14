<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BalanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_balance_summary()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 500
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 200
        ]);

        $response = $this->actingAs($user)
            ->withHeader('X-Security-Token', $user->api_token)
            ->getJson("/api/users/{$user->id}/balance");

        $response->assertStatus(200);
    }
}
