<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds to create example transactions.
     *
     * @return void
     */
    public function run()
    {
        // Get all created accounts
        $accounts = Account::all();

        foreach ($accounts as $account) {
            // Generate some random transactions
            for ($i = 0; $i < 5; $i++) {
                Transaction::create([
                    'transaction_number' => 'TX-' . Str::random(6) . '-' . $i,
                    'account_id'        => $account->id,
                    'amount'            => rand(10, 500), // Amount between 10 and 500
                    'type'              => rand(0, 1) ? 'deposit' : 'withdraw' // Randomly choose type
                ]);
            }
        }
    }
}
