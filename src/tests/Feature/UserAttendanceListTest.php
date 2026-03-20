<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\UsersTableSeeder;

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // 勤怠一覧ページ表示,現在月表示
    public function testAttendanceList() :void
    {
        $user = User::find(1);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');

        $carbon = new CarbonImmutable();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereYear('date', $carbon)
            ->whereMonth('date', $carbon)
            ->first();

        $response->assertViewHas('carbon', $attendance->date);
        $response->assertSee($attendance->date->format('Y/m'));
    }

    // 前月勤怠情報表示
    public function testPreviousMonth() :void
    {
        $user = User::find(1);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');

        $carbon = new CarbonImmutable();
        $previousMonth = $carbon->subMonthNoOverflow(1);
        $attendance = Attendance::where('user_id', $user->id)
            ->whereYear('date', $previousMonth)
            ->whereMonth('date', $carbon)
            ->first();

        $response->assertViewHas('carbon', $attendance->date);
        $response->assertSee($attendance->date->format('Y/m'));
    }

    // 翌月勤怠情報表示
    public function testNextMonth() :void
    {
        $user = User::find(1);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');

        $carbon = new CarbonImmutable();
        $nextMonth = $carbon->addMonthNoOverflow(1);
        $attendance = Attendance::where('user_id', $user->id)
            ->whereYear('date', $nextMonth)
            ->whereMonth('date', $carbon)
            ->first();

        $response->assertViewHas('carbon', $attendance->date);
        $response->assertSee($attendance->date->format('Y/m'));
    }

    // 詳細画面表示
    public function testDetail() :void
    {
        $user = User::find(1);

        $this->get('/attendance/list')->assertViewIs('attendances.list');

        $carbon = new CarbonImmutable();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $carbon)
            ->first();
        $this->get(route('attendance_detail', ['attendance' => $attendance->id]))
            ->assertViewHas('attendance', $attendance);
    }
}
