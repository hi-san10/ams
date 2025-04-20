<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'max:255', 'string'],
            'password' => ['required', 'min:4']
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスは『ユーザー名@ドメイン』形式で入力してください',
            'email.max' => 'メールアドレスは255文字以下で入力してください',
            'email.string' => 'メールアドレスは文字列で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは4文字以上入力してください'
        ];
    }
}
