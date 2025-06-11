<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $user = User::find(1);

        $this->get('/login')->assertStatus(200);

        $this->post(url('/login'), ['email' => $user->email, 'password' => '00000000']);
        $this->assertAuthenticatedAs($user);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // 勤怠一覧ページ表示,現在月表示
    public function testAttendanceList()
    {
        $user = User::find(1);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');
        $carbon = new CarbonImmutable();
        $attendances = Attendance::with('rests')->where('user_id', $user->id)->whereYear('date', $carbon)->whereMonth('date', $carbon)->get();
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
        $response->assertViewHas('attendances', $attendances);
        $response->assertSee($carbon->format('Y/m'));
    }

    // 前月勤怠情報表示
    public function testPreviousMonth()
    {
        $user = User::find(1);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');
        $carbon = new CarbonImmutable();
        $previousMonth = $carbon->subMonthNoOverflow(1);
        $attendances = Attendance::with('rests')->where('user_id', $user->id)->whereYear('date', $previousMonth)->whereMonth('date', $carbon)->get();
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
        $response->assertViewHas('attendances', $attendances);
    }

    // 翌月勤怠情報表示
    public function testNextMonth()
    {
        $user = User::find(1);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');
        $carbon = new CarbonImmutable();
        $nextMonth = $carbon->addMonthNoOverflow(1);
        $attendances = Attendance::with('rests')->where('user_id', $user->id)->whereYear('date', $nextMonth)->whereMonth('date', $carbon)->get();
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
        $response->assertViewHas('attendances', $attendances);
    }

    // 詳細画面表示
    public function testDetail()
    {
        $user = User::find(1);

        $this->get('/attendance/list')->assertViewIs('attendances.list');

        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertViewIs('attendances.detail');
    }
}
