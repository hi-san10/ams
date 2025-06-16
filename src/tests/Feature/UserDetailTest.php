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

        $this->get(route('attendance_detail', ['id' => $attendance->id]))->assertSee($user->name);
    }

    // 日付表示
    public function testDetailDate()
    {
        $carbon = new CarbonImmutable();
        $attendance = Attendance::where('date', $carbon)->first();

        $this->get(route('attendance_detail', ['id' => $attendance->id]))
            ->assertSee($attendance->date->format('m').'月'.$attendance->date->format('d').'日');
    }

    // 出勤退勤時刻確認
    public function testDetailWorkTime()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->get(route('attendance_detail', ['id' => $attendance->id]));
        $response->assertSee(substr($attendance->start_time, 0, 5));
        $response->assertSee(substr($attendance->end_time, 0, 5));
    }

    // 休憩時間確認
    public function testDetailRestTime()
    {
        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->first();
        $rest = Rest::where('attendance_id', $attendance->id)->first();

        $response = $this->get(route('attendance_detail', ['id' => $attendance->id]));
        $response->assertSee(substr($rest->start_time, 0, 5));
        $response->assertSee(substr($rest->end_time, 0, 5));
    }
}
