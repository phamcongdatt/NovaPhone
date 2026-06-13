<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Bỏ qua Vite manifest trong test (asset frontend chưa build).
        $this->withoutVite();
    }

    public function test_form_dang_ky_hien_thi_duoc(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Tạo tài khoản mới', false);
    }

    public function test_dang_ky_thanh_cong_tao_user_va_dang_nhap(): void
    {
        Event::fake();

        $response = $this->post('/register', [
            'name'                  => 'Nguyễn Văn A',
            'email'                 => 'nguyenvana@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Tạo đúng user trong DB
        $this->assertDatabaseHas('users', [
            'email' => 'nguyenvana@example.com',
            'name'  => 'Nguyễn Văn A',
            'role'  => 'user',
        ]);

        $user = User::where('email', 'nguyenvana@example.com')->first();

        // Password đã được hash (không lưu plaintext)
        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));

        // Đã đăng nhập + bắn event Registered + redirect tới trang xác thực
        $this->assertAuthenticatedAs($user);
        Event::assertDispatched(Registered::class);
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_event_registered_gui_email_xac_thuc(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name'                  => 'Trần Thị B',
            'email'                 => 'tranthib@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'tranthib@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_validate_that_bai_khi_du_lieu_sai(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name'                  => '',
            'email'                 => 'khong-phai-email',
            'password'              => '123',          // < 8 ký tự
            'password_confirmation' => '999',          // không khớp
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_email_phai_la_duy_nhat(): void
    {
        User::factory()->create(['email' => 'trung@example.com']);

        $response = $this->from('/register')->post('/register', [
            'name'                  => 'Lê Văn C',
            'email'                 => 'trung@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_trang_thong_bao_xac_thuc_user_dang_nhap_xem_duoc(): void
    {
        // User đã đăng nhập (chưa xác thực email) xem được trang thông báo
        $user = User::factory()->unverified()->create();
        $this->actingAs($user)->get('/email/verify')
            ->assertOk()
            ->assertSee('Xác thực địa chỉ email', false);
    }

}