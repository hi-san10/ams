<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\LoginRequest;
use App\Models\AdminUser;
use Database\Seeders\AdminUsersTableSeeder;
use Illuminate\Support\Facades\Validator;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->seed(AdminUsersTableSeeder::class);
    }
    /**
     * A basic feature test example.
     *@dataProvider validationProvider
     * @return void
     */

    //  管理者ログイン画面バリデーション
    public function testAdminLoginValidation($inData, $outFail, $outMessage)
    {
        $this->get('/admin/login')->assertStatus(200);

        $request = new LoginRequest();
        $rules = $request->rules();
        $messages = $request->messages();

        $validator = Validator::make($inData, $rules, $messages);
        $result = $validator->fails();
        $this->assertEquals($outFail, $result);

        $messages = $validator->errors()->getMessages();
        $this->assertEquals($outMessage, $messages);
    }

    public function validationProvider()
    {
        return [
            'email_empty' => [
                [
                    'email' => '',
                    'password' => '12345678'
                ],
                true,
                [
                    'email' => ['メールアドレスを入力してください']
                ],
            ],

            'password_empty' => [
                [
                    'email' => 'aaaa@example.com',
                    'password' => ''
                ],
                true,
                [
                    'password' => ['パスワードを入力してください']
                ],
            ],
        ];
    }

    // 登録情報と異なるメールアドレスでログイン
    public function testUnRegistered()
    {
        $this->get('/admin/login')->assertStatus(200);

        $data = [
            'email' => 'aaaa@example.com',
            'password' => '12345678'
        ];
        $response = $this->postJson(url('/admin/login'), $data);
        $response->assertSessionHas('message', 'ログイン情報が登録されていません');
    }
}
