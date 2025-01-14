<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\User;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds to create example accounts.
     *
     * @return void
     */
    public function run()
    {
        // Assuming users are already inserted (e.g., UserSeeder).
        // Create an account for each user.
        $users = User::all();
        foreach ($users as $user) {
            Account::create([
                'user_id' => $user->id,
                'balance' => rand(100, 1000), // Initial balance between 100 and 1000
            ]);
        }
    }
}
