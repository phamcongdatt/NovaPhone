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
}
