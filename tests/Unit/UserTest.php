<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes()
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_user_has_many_accounts()
    {
        $user = User::factory()->create();
        Account::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->accounts);
        $this->assertInstanceOf(Account::class, $user->accounts->first());
    }
}
