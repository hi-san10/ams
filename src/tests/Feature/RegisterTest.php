<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @dataProvider validationProvider
     * @return void
     */

    //  会員登録画面バリデーション
    public function testRegisterValidation($inData, $outFail, $outMessage)
    {
        $this->get('/register')->assertStatus(200);

        $request = new RegisterRequest();
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
            'name_empty' => [
                [
                    'name' => '',
                    'email' => 'aaaa@example.com',
                    'password' => '12345678',
                    'password_confirmation' => '12345678'
                ],
                true,
                [
                    'name' => ['お名前を入力してください'],
                ],
            ],

            'email_empty' => [
                [
                    'name' => 'aaa',
                    'email' => '',
                    'password' => '12345678',
                    'password_confirmation' => '12345678'

                ],
                true,
                [
                    'email' => ['メールアドレスを入力してください'],
                ],
            ],

            'password_empty' => [
                [
                    'name' => 'aaa',
                    'email' => 'aaaa@example.com',
                    'password' => '',
                    'password_confirmation' => '12345678'
                ],
                true,
                [
                    'password' => ['パスワードを入力してください'],
                    'password_confirmation' => ['パスワードと一致しません']
                ],
            ],

            'password_min' => [
                [
                    'name' => 'aaa',
                    'email' => 'aaaa@example.com',
                    'password' => '1234567',
                    'password_confirmation' => '1234567'
                ],
                true,
                [
                    'password' => ['パスワードは8文字以上で入力してください'],
                    'password_confirmation' => ['パスワードは8文字以上で入力してください']
                ],
            ],

            'password_mismatch' => [
                [
                    'name' => 'aaa',
                    'email' => 'aaaa@example.com',
                    'password' => '12345678',
                    'password_confirmation' => '12345677'
                ],
                true,
                [
                    'password_confirmation' => ['パスワードと一致しません']
                ],
            ],

            'store' => [
                [
                    'name' => 'aaa',
                    'email' => 'aaaa@example.com',
                    'password' => '12345678',
                    'password_confirmation' => '12345678'
                ],
                false,
                [],
            ],

        ];
    }

    // 会員登録処理
    public function testUserRegister()
    {
        $this->get('/register')->assertStatus(200);

        $data = [
            'name' => 'aaa',
            'email' => 'aaaa@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $response = $this->postJson(route('store'), $data);
        $response->assertViewIs('auth_induction');
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => 'aaa',
            'email' => 'aaaa@example.com'
        ]);
    }
}
