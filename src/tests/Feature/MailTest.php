<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Mail\CertificationMail;
use Illuminate\Support\Facades\Mail;

class MailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  会員登録後認証メール送信
    public function testSendMail()
    {
        Mail::fake();

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'aaa',
            'email' => 'aaaa@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $response = $this->postJson(route('store'), $data);

        Mail::assertSent(
            CertificationMail::class,
            function ($mail) use ($data) {
                return $mail->to[0]['address'] === $data['email'];
            }
        );
    }

    // メール認証サイト遷移、認証処理
    public function testMailtrap()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'aaa',
            'email' => 'aaaa@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $response = $this->postJson(route('store'), $data);
        $response->assertViewIs('auth_induction');

        $this->get('https://mailtrap.io');
        $response = $this->get(route('verification', ['email' => $data['email']]));
        $response->assertSessionHas('message', 'メールによる認証が完了しました');
        $response->assertRedirect('login');

        $this->get('login')->assertViewIs('login');
    }
}
