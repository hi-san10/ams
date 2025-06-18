<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AdminUsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminCorrectionTest extends TestCase
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

    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  修正申請一覧画面
    public function testCorrectionList()
    {
        User::factory(10)->create();
        Attendance::factory(50)->create();
        StampCorrectionRequest::factory(10)->create();

        $correction_requests = StampCorrectionRequest::with('user', 'attendance')->where('is_approval', false)->get();
        $this->get('/stamp_correction_request/list')->assertViewHas('correction_requests', $correction_requests);

        $correction_requests = StampCorrectionRequest::with('user', 'attendance')->where('is_approval', true)->get();
        $this->get('/stamp_correction_request/list?page=approved')->assertViewHas('correction_requests', $correction_requests);
    }

    // 修正申請詳細確認後、承認処理
    public function testApprovalDetail()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $carbon = new CarbonImmutable();
        $correction = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'is_approval' => false,
            'request_date' => $carbon,
            'target_date' => $carbon,
            'request_reason' => '電車遅延'
        ]);

        $correction_attendance = CorrectionAttendance::create([
            'stamp_correction_request_id' => $correction->id,
            'start_time' => '09:00',
            'end_time' => '18:00'
        ]);
        CorrectionRest::create([
            'correction_attendance_id' => $correction_attendance->id,
            'start_time' => '11:00',
            'end_time' => '12:00'
        ]);

        $response = $this->get(route('approval_detail', ['attendance_correct_request' => $correction_attendance->id]));
        $response->assertViewHas('attendance', $correction_attendance);
        $response->assertViewHas('correction', $correction);

        $this->post(route('approve', ['id' => $correction->id]));
        $this->assertDatabaseHas('attendances', [
            'id' => $correction->attendance_id,
            'start_time' => $correction_attendance->start_time,
            'end_time' => $correction_attendance->end_time,
        ]);
    }
}

