<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_form_dang_nhap_hien_thi_duoc(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('NovaPhone');
    }

    public function test_dang_nhap_thanh_cong(): void
    {
        $user = User::factory()->create([
            'email' => 'user@novaphone.vn',
        ]);

        $this->post('/login', [
            'email' => 'user@novaphone.vn',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_dang_nhap_that_bai_khi_sai_mat_khau(): void
    {
        User::factory()->create([
            'email' => 'user@novaphone.vn',
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'user@novaphone.vn',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_tai_khoan_bi_khoa_khong_dang_nhap_duoc(): void
    {
        User::factory()->create([
            'email' => 'blocked@novaphone.vn',
            'status' => 'blocked',
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'blocked@novaphone.vn',
            'password' => 'password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dang_xuat_thanh_cong(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_social_login_post_success(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \Illuminate\Auth\Events\Registered::class,
        ]);

        $this->postJson('/auth/login', [
            'provider' => 'google',
            'provider_id' => 'mock_google_123',
            'email' => 'google.user@gmail.com',
            'name' => 'Google User',
            'avatar' => 'https://example.com/avatar.jpg',
        ])->assertOk()
          ->assertJson([
              'success' => true,
              'message' => 'Đăng nhập thành công!',
              'redirect' => route('home')
          ]);

        $this->assertDatabaseHas('users', [
            'email' => 'google.user@gmail.com',
            'provider' => 'google',
            'provider_id' => 'mock_google_123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_social_login_post_existing_user(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \Illuminate\Auth\Events\Registered::class,
        ]);

        $user = User::factory()->create([
            'email' => 'existing@gmail.com',
            'name' => 'Existing User',
        ]);

        $this->postJson('/auth/login', [
            'provider' => 'facebook',
            'provider_id' => 'mock_facebook_456',
            'email' => 'existing@gmail.com',
            'name' => 'Facebook User',
            'avatar' => 'https://example.com/fb.jpg',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'email' => 'existing@gmail.com',
            'provider' => 'facebook',
            'provider_id' => 'mock_facebook_456',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}
