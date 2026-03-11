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
            'rests.*.start_time' => ['date_format:H:i', 'after:start', 'before:end', 'nullable', 'required_with:rests.*.end_time'],
            'rests.*.end_time' => ['date_format:H:i', 'after:start', 'before:end', 'nullable', 'required_with:rests.*.start_time'],
            'remarks' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'start.date_format' => '時:分(00:00)で入力してください',
            'start.before' => '出勤時間もしくは退勤時間が不適切な値です',

            'end.date_format' => '時:分(00:00)で入力してください',

            'rests.*.start_time.date_format' => '時:分(00:00)で入力してください',
            'rests.*.start_time.after' => '休憩時間が不適切な値です',
            'rests.*.start_time.before' => '休憩時間が不適切な値です',
            'rests.*.start_time.required_with' => '休憩時間を入力してください',

            'rests.*.end_time.date_format' => '時:分(00:00)で入力してください',
            'rests.*.end_time.after' => '休憩時間が不適切な値です',
            'rests.*.end_time.before' => '休憩時間が不適切な値です',
            'rests.*.end_time.required_with' => '休憩時間を入力してください',

            'remarks.required' => '備考を記入してください'
        ];
    }
}
