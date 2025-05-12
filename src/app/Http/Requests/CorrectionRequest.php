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
            'start' => ['date_format:H:i', 'before:end'],
            'end' => ['date_format:H:i'],
            'rest_start.*' => ['date_format:H:i', 'after:start', 'before:end'],
            'rest_end.*' => ['date_format:H:i', 'after:rest_start.*', 'before:end'],
            'newRest_start' => ['date_format:H:i', 'after:start', 'before:end', 'nullable', 'required_with:newRest_end'],
            'newRest_end' => ['date_format:H:i', 'after:newRest_start', 'before:end', 'nullable', 'required_with:newRest_start'],
            'remarks' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'start.date_format' => '時:分(00:00)で入力してください',
            'start.before' => '出勤時間もしくは退勤時間が不適切な値です',

            'end.date_format' => '時:分(00:00)で入力してください',

            'rest_start.*.date_format' => '時:分(00:00)で入力してください',
            'rest_start.*.after' => '休憩時間が勤務時間外です',
            'rest_start.*.before' => '休憩時間が勤務時間外です',

            'rest_end.*.date_format' => '時:分(00:00)で入力してください',
            'rest_end.*.after' => '休憩時間が不適切な値です',
            'rest_end.*.before' => '休憩時間が勤務時間外です',

            'newRest_start.date_format' => '時:分(00:00)で入力してください',
            'newRest_start.after' => '休憩時間が勤務時間外です',
            'newRest_start.before' => '休憩時間が勤務時間外です',
            'newRest_start.required_with' => '休憩時間を入力してください',

            'newRest_end.date_format' => '時:分(00:00)で入力してください',
            'newRest_end.after' => '休憩時間が不適切な値です',
            'newRest_end.before' => '休憩時間が勤務時間外です',
            'newRest_end.required_with' => '休憩時間を入力してください',

            'remarks.required' => '備考を記入してください'
        ];
    }
}
