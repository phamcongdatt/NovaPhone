<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_khach_chua_dang_nhap_duoc_chuyen_ve_trang_login(): void
    {
        $this->get('/account')
            ->assertRedirect('/login');
    }

    public function test_user_dang_nhap_xem_duoc_trang_tai_khoan(): void
    {
        $user = User::factory()->create([
            'name' => 'Nguyen Van A',
            'email' => 'user@novaphone.vn',
            'phone' => '0900000002',
        ]);

        $this->actingAs($user)->get('/account')
            ->assertOk()
            ->assertSee('Tài khoản của tôi')
            ->assertSee('Nguyen Van A')
            ->assertSee('user@novaphone.vn')
            ->assertSee('0900000002');
    }
}
