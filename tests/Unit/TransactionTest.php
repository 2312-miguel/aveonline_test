<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_has_fillable_attributes()
    {
        $transaction = new Transaction([
            'transaction_number' => 'TRX001',
            'account_id' => 1,
            'amount' => 100,
            'type' => 'deposit'
        ]);

        $this->assertNotNull($transaction->transaction_number);
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals('deposit', $transaction->type);
    }

    public function test_transaction_belongs_to_account()
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $transaction->account);
        $this->assertEquals($account->id, $transaction->account->id);
    }
}
