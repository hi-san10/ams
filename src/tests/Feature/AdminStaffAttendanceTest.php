<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\Attendance;
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

        $this->get('/login')->assertStatus(200);
        $this->postJson('/admin/login', ['email' => $admin->email, 'password' => '00000000']);
        $this->assertTrue(Auth::guard('admins')->check());
    }

    //  スタッフ一覧画面
    public function testStaffList()
    {
        $this->seed(UsersTableSeeder::class);
        $users = User::select('id', 'name', 'email')->get();

        $this->get('/admin/staff/list')->assertViewHas('users', $users);
    }

    // スタッフ別勤怠一覧確認
    public function testStaffAttendance()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $user = User::find(1);
        $carbon = new CarbonImmutable();

        $attendances = Attendance::with('rests')->where('user_id', $user->id)->whereYear('date', $carbon)->whereMonth('date', $carbon)
            ->orderBy('date', 'asc')->get();
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
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }
        $this->get(route('staff_attendance_list', ['id' => $user->id]))->assertViewHas('attendances', $attendances);
    }

    /**
     * A basic feature test example.
     * @dataProvider monthProvider
     * @return void
     */

    // スタッフ前月翌月勤怠確認
    public function testPreviousMonth($month)
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $user = User::find(1);

        $attendances = Attendance::with('rests')->whereMonth('date', $month)->get();
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
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }
        $this->get(route('staff_attendance_list', ['id' => $user->id, 'month' => $month]))->assertViewHas('attendances', $attendances);
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
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $attendance = Attendance::with('user', 'rests')->where('id', 1)->first();

        $this->get('/admin/attendance/list')->assertStatus(200);
        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertViewHas('attendance', $attendance);
    }
}
