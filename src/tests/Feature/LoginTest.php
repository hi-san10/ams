<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->get('/register')->assertStatus(200);

        $data = [
            'name' => 'aaa',
            'email' => 'aaaa@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $this->postJson(route('store'), $data);
    }
    /**
     * A basic feature test example.
     *@dataProvider validationProvider
     * @return void
     */

    //  ログイン画面バリデーション
    public function testLoginValidation($inData, $outFail, $outMessage)
    {
        $this->get('/login')->assertStatus(200);

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
        $this->get('/login')->assertStatus(200);

        $data = [
            'email' => 'bbbb@example.com',
            'password' => '12345678'
        ];
        $response = $this->postJson(url('/login'), $data);
        $response->assertSessionHas('message', 'ログイン情報が登録されていません');
    }
}
