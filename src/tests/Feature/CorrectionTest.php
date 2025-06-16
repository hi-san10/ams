<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CorrectionRequest;
use App\Models\AdminUser;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\StampCorrectionRequest;
use Carbon\CarbonImmutable;
use Database\Seeders\AdminUsersTableSeeder;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class CorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();
        $rest = Rest::where('attendance_id', $attendance->id)->first();

        $this->get('/login')->assertStatus(200);

        $this->post(url('/login'), ['email' => $user->email, 'password' => '00000000']);
        $this->assertAuthenticatedAs($user);
    }
    /**
     * A basic feature test example.
     * @dataProvider validationProvider
     * @return void
     */

    //  修正処理バリデーション
    public function testCorrectionValidation($inData, $outFail, $outMessage)
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

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

    // 修正処理実行
    public function testCorrection()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertStatus(200);

        $data = [
            'start' => '08:00',
            'end' => '17:00',
            'newRest_start' => '10:00',
            'newRest_end' => '10:10',
            'remarks' => '電車遅延'
        ];
        $this->postJson(route('correction', ['id' => $attendance->id]), $data)->assertStatus(302);
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => 1,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'is_approval' => false,
            'target_date' => $attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => $data['remarks']
        ]);
        $this->post('/logout')->assertStatus(302);
        $this->assertGuest();

        $this->seed(AdminUsersTableSeeder::class);
        $admin = AdminUser::find(1);

        $this->get('/login')->assertStatus(200);
        $this->post(url('/admin/login'), ['email' => $admin->email, 'password' => '00000000']);
        $this->assertTrue(Auth::guard('admins')->check());

        $correction = StampCorrectionRequest::with('user')->where('attendance_id', $attendance->id)->first();
        $this->get(route('approval_detail', ['attendance_correct_request' => $correction->id]))
            ->assertViewHas('correction', $correction);

        $correction_requests = StampCorrectionRequest::with('user', 'attendance')
            ->where([
                ['user_id', $user->id],
                ['attendance_id', $attendance->id],
                ['is_approval', false]
            ])->get();
        $this->get(route('request_list'))->assertViewHas('correction_requests', $correction_requests);
    }

    // 申請一覧画面承認待ち表示
    public function testRequestList()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertStatus(200);

        $data = [
            'start' => '08:00',
            'end' => '17:00',
            'newRest_start' => '10:00',
            'newRest_end' => '10:10',
            'remarks' => '電車遅延'
        ];
        $this->postJson(route('correction', ['id' => $attendance->id]), $data)->assertStatus(302);
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => 1,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'is_approval' => false,
            'target_date' => $attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => $data['remarks']
        ]);

        $correction_requests = StampCorrectionRequest::with('user', 'attendance')->where('user_id', $user->id)->where('is_approval', false)->get();
        $this->get(route('request_list'))->assertViewHas('correction_requests', $correction_requests);
    }

    // 申請一覧画面承認済み表示
    public function testApproved()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertStatus(200);

        $data = [
            'start' => '08:00',
            'end' => '17:00',
            'newRest_start' => '10:00',
            'newRest_end' => '10:10',
            'remarks' => '電車遅延'
        ];
        $this->postJson(route('correction', ['id' => $attendance->id]), $data)->assertStatus(302);
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => 1,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'is_approval' => false,
            'target_date' => $attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => $data['remarks']
        ]);

        StampCorrectionRequest::find(1)->update(['is_approval' => true]);
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => 1,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'is_approval' => true,
            'target_date' => $attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => $data['remarks']
        ]);
        $correction_requests = StampCorrectionRequest::with('user', 'attendance')->where('user_id', $user->id)->where('is_approval', true)->get();
        $this->get('stamp_correction_request/list?page=approved')
            ->assertViewHas('correction_requests', $correction_requests);
    }

    // 勤怠修正処理後に詳細確認
    public function testCorrectionDetail()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertStatus(200);

        $data = [
            'start' => '08:00',
            'end' => '17:00',
            'newRest_start' => '10:00',
            'newRest_end' => '10:10',
            'remarks' => '電車遅延'
        ];
        $this->postJson(route('correction', ['id' => $attendance->id]), $data)->assertStatus(302);
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => 1,
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'is_approval' => false,
            'target_date' => $attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => $data['remarks']
        ]);

        $this->get('stamp_correction_request/list')->assertStatus(200);
        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertStatus(200);
    }
}
