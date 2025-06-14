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

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->seed(AdminUsersTableSeeder::class);
        $admin = AdminUser::find(1);

        $this->get('/login')->assertStatus(200);
        $this->postJson('/admin/login', ['email' => $admin->email, 'password' => '11111111']);
        $this->assertTrue(Auth::guard('admins')->check());
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  全ユーザー当日勤怠一覧表示
    public function testAttendanceList()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $carbon = new CarbonImmutable();

        $attendances = Attendance::with('user', 'rests')->whereDate('date', $carbon)->get();
        foreach ($attendances as $attendance) {
            $start = new CarbonImmutable($attendance->start_time);
            $end = new CarbonImmutable($attendance->end_time);
            $workingTime = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            $number = 0;
            foreach ($rests as $rest) {
                $restStart = new CarbonImmutable($rest->start_time);
                $restEnd = new CarbonImmutable($rest->end_time);
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;

                $attendance->is_rest = $rest->end_time;
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }
        $this->get('/admin/attendance/list')->assertViewHas('attendances', $attendances);
    }

    // 勤怠一覧画面日付確認
    public function testDate()
    {
        $carbon = new CarbonImmutable();
        $this->get('admin/attendance/list')->assertSee($carbon->format('Y年m月d日'));
    }

    // 勤怠一覧画面前日情報表示
    public function testPreviousDay()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $carbon = new CarbonImmutable();

        $previousDay = new CarbonImmutable($carbon->subDay(1));
        $attendances = Attendance::with('user', 'rests')->whereDay('date', $previousDay)->get();
        foreach ($attendances as $attendance) {
            $start = new CarbonImmutable($attendance->start_time);
            $end = new CarbonImmutable($attendance->end_time);
            $workingTime = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            $number = 0;
            foreach ($rests as $rest) {
                $restStart = new CarbonImmutable($rest->start_time);
                $restEnd = new CarbonImmutable($rest->end_time);
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;

                $attendance->is_rest = $rest->end_time;
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }
        $this->get(route('admin_attendance_list', ['day' => $previousDay]))->assertViewHas('attendances', $attendances);
    }

    // 勤怠一覧画面翌日情報表示
    public function testNextDay()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $carbon = new CarbonImmutable();

        $nextDay = new CarbonImmutable($carbon->addDay(1));
        $attendances = Attendance::with('user', 'rests')->whereDay('date', $nextDay)->get();
        foreach ($attendances as $attendance) {
            $start = new CarbonImmutable($attendance->start_time);
            $end = new CarbonImmutable($attendance->end_time);
            $workingTime = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            $number = 0;
            foreach ($rests as $rest) {
                $restStart = new CarbonImmutable($rest->start_time);
                $restEnd = new CarbonImmutable($rest->end_time);
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;

                $attendance->is_rest = $rest->end_time;
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }
        $this->get(route('admin_attendance_list', ['day' => $nextDay]))->assertViewHas('attendances', $attendances);
    }
}
