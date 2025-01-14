<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_has_fillable_attributes()
    {
        $log = new Log([
            'user_id' => 1,
            'endpoint' => '/api/test',
            'method' => 'GET',
            'ip_address' => '127.0.0.1'
        ]);

        $this->assertEquals('/api/test', $log->endpoint);
        $this->assertEquals('GET', $log->method);
        $this->assertEquals('127.0.0.1', $log->ip_address);
    }

    public function test_log_belongs_to_user()
    {
        $user = User::factory()->create();
        $log = Log::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }
}
