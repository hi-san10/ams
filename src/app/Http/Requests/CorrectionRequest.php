<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
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
            'start' => ['date_format:G:i', 'before:end'],
            'end' => ['date_format:G:i', 'after:start'],
            'rest_start' => ['date_format:G:i', 'after:start'],
            'rest_end' => ['date_format:G:i', 'before:end'],
            'newRest_start' => ['date_format:G:i', 'after:start'],
            'newRest_end' => ['date_format:G:i', 'before:end'],
            'remarks' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'start.date_format' => '時:分で入力してください',
            'start.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'end.date_format' => '時:分で入力してください',
            'end.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'rest_start.date_format' => '時:分で入力してください',
            'rest_start.after' => '休憩時間が勤務時間外です',
            'rest_end.date_format' => '時:分で入力してください',
            'rest_end.before' => '休憩時間が勤務時間外です',
            'newRest_start.date_format' => '時:分で入力してください',
            'newRest_start.after' => '休憩時間が勤務時間外です',
            'newRest_end.date_format' => '時:分で入力してください',
            'newRest_end.before' => '休憩時間が勤務時間外です',
            'remarks.required' => '備考を記入してください'
        ];
    }
}
