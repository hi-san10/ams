<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\CarbonImmutable;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AdminUsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminStaffAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminUsersTableSeeder::class);
        $admin = AdminUser::find(1);

        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);

        $this->get('/login')->assertStatus(200);
        $this->postJson('/admin/login', ['email' => $admin->email, 'password' => '00000000']);
        $this->assertTrue(Auth::guard('admins')->check());
    }

    //  スタッフ一覧画面
    public function testStaffList()
    {
        $users = User::select('id', 'name', 'email')->get();

        $this->get('/admin/staff/list')->assertViewHas('users', $users);
    }

    // スタッフ別勤怠一覧確認
    public function testStaffAttendance()
    {
        $user = User::find(1);
        $carbon = new CarbonImmutable();

        $attendances = Attendance::with('user', 'rests')
            ->where('user_id', $user->id)
            ->whereYear('date', $carbon)
            ->whereMonth('date', $carbon)
            ->orderBy('date', 'asc')
            ->get();

        $service = new AttendanceService;
        $result = $service->calculate($attendances);

        $this->get(route('staff_attendance_list', ['id' => $user->id]))
            ->assertViewHas('attendances', $result);
    }

    /**
     * A basic feature test example.
     * @dataProvider monthProvider
     * @return void
     */

    // スタッフ前月翌月勤怠確認
    public function testPreviousMonth($month)
    {
        $user = User::find(1);

        $attendances = Attendance::with('user', 'rests')
            ->whereMonth('date', $month)
            ->get();

        $service = new AttendanceService;
        $result = $service->calculate($attendances);

        $this->get(route('staff_attendance_list', ['id' => $user->id, 'month' => $month]))
            ->assertViewHas('attendances', $result);
    }

    public function monthProvider()
    {
        $carbon = new CarbonImmutable();

        return [
            'previousMonth' => [
                new CarbonImmutable($carbon->subMonth(1))
            ],

            'nextMonth' => [
                new CarbonImmutable($carbon->addMonth(1))
            ]
        ];
    }

    // 勤怠詳細画面
    public function testDetail()
    {
        $attendance = Attendance::with('user', 'rests')
            ->where('id', 1)
            ->first();

        $this->get('/admin/attendance/list')->assertStatus(200);
        $this->get(route('attendance_detail', ['attendance' => $attendance->id]))
            ->assertViewHas('attendance', $attendance);
    }
}
