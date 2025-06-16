<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Database\Seeders\AdminUsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Validator;

class AdminCorrectionDetailTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminUsersTableSeeder::class);
        $admin = AdminUser::find(1);

        $this->get('/login')->assertStatus(200);
        $this->postJson('/admin/login', ['email' => $admin->email, 'password' => '00000000']);
        $this->assertTrue(Auth::guard('admins')->check());
    }


    //  勤怠詳細確認
    public function testDetail()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $attendance = Attendance::with('user', 'rests')->where('id', 1)->first();

        $this->get('/admin/attendance/list')->assertStatus(200);
        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertViewHas('attendance', $attendance);
    }

    /**
     * A basic feature test example.
     * @dataProvider validationProvider
     * @return void
     */

    // 管理者修正機能バリデーション
    public function testValidation($inData, $outFail, $outMessage)
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $attendance = Attendance::where('user_id', 1)->first();

        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertStatus(200);

        $request = new CorrectionRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator = validator::make($inData, $rules, $messages);
        $result = $validator->fails();
        $this->assertEquals($outFail, $result);
        $messages = $validator->errors()->getMessages();
        $this->assertEquals($outMessage, $messages);
    }

    public function validationProvider()
    {
        return [
            'start_timeError' => [
                [
                    'start' => '18:00',
                    'end' => '17:00',
                    'rest_start' => '10:00',
                    'rest_end' => '10:10',
                    'remarks' => '電車遅延'
                ],
                true,
                [
                    'start' => ['出勤時間もしくは退勤時間が不適切な値です']
                ]
            ],

            'restError' => [
                [
                    'start' => '08:00',
                    'end' => '17:00',
                    'newRest_start' => '17:10',
                    'newRest_end' => '17:20',
                    'remarks' => '電車遅延'
                ],
                true,
                [
                    'newRest_start' => ['休憩時間が勤務時間外です'],
                    'newRest_end' => ['休憩時間が勤務時間外です']
                ]
            ],

            'remarksError' => [
                [
                    'start' => '08:00',
                    'end' => '17:00',
                    'newRest_start' => '10:00',
                    'newRest_end' => '10:10',
                    'remarks' => ''
                ],
                true,
                [
                    'remarks' => ['備考を記入してください'],
                ]
            ]
        ];
    }
}

