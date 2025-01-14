<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_download_logs()
    {
        $user = User::factory()->create();
        Log::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->get('/api/logs/download');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
