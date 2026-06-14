<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════
    //  ĐĂNG KÝ
    // ═══════════════════════════════════════════

    public function test_register_page_loads(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Nguyễn Văn A',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertAuthenticated();
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'exists@example.com']);

        $this->post(route('register'), [
            'name'                  => 'Test User',
            'email'                 => 'exists@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_register_fails_when_password_not_confirmed(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'wrong',
        ])->assertSessionHasErrors('password');
    }

    public function test_register_fails_with_short_password(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => '123',
            'password_confirmation' => '123',
        ])->assertSessionHasErrors('password');
    }

    // ═══════════════════════════════════════════
    //  ĐĂNG NHẬP
    // ═══════════════════════════════════════════

    public function test_login_page_loads(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login'), [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $this->post(route('login'), [
            'email'    => 'user@example.com',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $this->post(route('login'), [
            'email'    => 'nobody@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_with_remember_me_sets_cookie(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'user@example.com',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertCookie(Auth()->guard()->getRecallerName());
    }

    // ═══════════════════════════════════════════
    //  ĐĂNG XUẤT
    // ═══════════════════════════════════════════

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post(route('logout'))
             ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // ═══════════════════════════════════════════
    //  QUÊN MẬT KHẨU
    // ═══════════════════════════════════════════

    public function test_forgot_password_page_loads(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_link_sent_for_existing_email(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $this->post(route('password.email'), ['email' => 'user@example.com'])
             ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email'       => 'user@example.com',
        ]);

        $user = User::where('email', 'user@example.com')->first();
        $this->assertNotNull($user->reset_token);
        $this->assertNotNull($user->reset_token_expires_at);
    }

    public function test_reset_link_shows_same_message_for_nonexistent_email(): void
    {
        // Bảo mật: không lộ thông tin email tồn tại hay không
        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
             ->assertSessionHas('status');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $token = \Illuminate\Support\Str::random(64);
        $user  = User::factory()->create([
            'reset_token'            => Hash::make($token),
            'reset_token_expires_at' => Carbon::now()->addMinutes(60),
        ]);

        $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertNull($user->fresh()->reset_token);
    }

    public function test_reset_password_fails_with_expired_token(): void
    {
        $token = \Illuminate\Support\Str::random(64);
        $user  = User::factory()->create([
            'reset_token'            => Hash::make($token),
            'reset_token_expires_at' => Carbon::now()->subMinutes(10), // đã hết hạn
        ]);

        $this->post(route('password.update'), [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('email');
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'reset_token'            => Hash::make('validtoken'),
            'reset_token_expires_at' => Carbon::now()->addMinutes(60),
        ]);

        $this->post(route('password.update'), [
            'token'                 => 'wrongtoken',
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('email');
    }

    // ═══════════════════════════════════════════
    //  BẢO VỆ ROUTE
    // ═══════════════════════════════════════════

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('login'))->assertRedirect(route('dashboard'));
    }
}