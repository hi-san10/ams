<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\CarbonImmutable;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDetailTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
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

    //  名前表示
    public function testDetailName()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->get(route('attendance_detail', ['attendance' => $attendance->id]))->assertSee($user->name);
    }

    // 日付表示
    public function testDetailDate()
    {
        $carbon = new CarbonImmutable();
        $attendance = Attendance::where('date', $carbon)->first();

        $this->get(route('attendance_detail', ['attendance' => $attendance->id]))
            ->assertSee($attendance->date->format('m').'月'.$attendance->date->format('d').'日');
    }

    // 出勤退勤時刻確認
    public function testDetailWorkTime()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();
        $start = CarbonImmutable::parse($attendance->start_time)->format('H:i');
        $end = CarbonImmutable::parse($attendance->end_time)->format('H:i');

        $response = $this->get(route('attendance_detail', ['attendance' => $attendance->id]));
        $response->assertSee($start);
        $response->assertSee($end);
    }

    // 休憩時間確認
    public function testDetailRestTime()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();
        $rest = Rest::where('attendance_id', $attendance->id)->first();
        $start = CarbonImmutable::parse($rest->start_time)->format('H:i');
        $end = CarbonImmutable::parse($rest->end_time)->format('H:i');

        $response = $this->get(route('attendance_detail', ['attendance' => $attendance->id]));
        $response->assertSee($start);
        $response->assertSee($end);
    }
}
