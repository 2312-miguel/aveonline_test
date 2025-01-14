<?php

namespace Tests\Feature\Controllers\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserRepositoryInterface;
use Mockery;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->app->instance(UserRepositoryInterface::class, $this->userRepository);
    }

    public function test_login_form_can_be_displayed()
    {
        $response = $this->get(route('login.show'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_users_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/users');
        $this->assertAuthenticated();
    }

    public function test_users_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('error');
        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login.show'));
        $this->assertGuest();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
