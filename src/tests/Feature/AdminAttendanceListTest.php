<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Carbon\CarbonImmutable;
use Database\Seeders\AdminUsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Services\AttendanceService;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
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
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  全ユーザー当日勤怠一覧表示
    public function testAttendanceList() :void
    {
        $carbon = new CarbonImmutable();

        $attendances = Attendance::with('user', 'rests')
            ->whereDate('date', $carbon)
            ->get();

        $service = new AttendanceService;
        $result = $service->calculate($attendances);

        $this->get('/admin/attendance/list')
            ->assertViewHas('attendances', $result);
    }

    // 勤怠一覧画面日付確認
    public function testDate() :void
    {
        $carbon = new CarbonImmutable();
        $this->get('admin/attendance/list')
            ->assertSee($carbon->format('Y年m月d日'));
    }

    // 勤怠一覧画面前日情報表示
    public function testPreviousDay() :void
    {
        $carbon = new CarbonImmutable();

        $previousDay = new CarbonImmutable($carbon->subDay(1));
        $attendances = Attendance::with('user', 'rests')
            ->whereDay('date', $previousDay)
            ->get();

        $service = new AttendanceService;
        $result = $service->calculate($attendances);

        $this->get(route('admin_attendance_list', ['day' => $previousDay]))
            ->assertViewHas('attendances', $result);
    }

    // 勤怠一覧画面翌日情報表示
    public function testNextDay() :void
    {
        $carbon = new CarbonImmutable();

        $nextDay = new CarbonImmutable($carbon->addDay(1));
        $attendances = Attendance::with('user', 'rests')
            ->whereDay('date', $nextDay)
            ->get();

        $service = new AttendanceService;
        $result = $service->calculate($attendances);

        $this->get(route('admin_attendance_list', ['day' => $nextDay]))
            ->assertViewHas('attendances', $result);
    }
}
