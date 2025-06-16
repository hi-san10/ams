<?php

namespace Tests\Feature;

use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class WorkEndTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->get('/login')->assertStatus(200);

        $this->post(url('/login'), ['email' => $this->user->email, 'password' => '11111111']);
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  退勤処理
    public function testWorkOut()
    {
        $this->get('/attendance')->assertStatus(200);
        $this->get('/attendance/start')->assertStatus(302);

        $carbon = new CarbonImmutable();
        $user_id = User::find(1);
        $user = Attendance::where('user_id', $user_id->id)->where('date', $carbon)->first();
        $workEnd = $user->end_time;
        $rest = null;

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('退勤');

        $this->get('/attendance/end')->assertStatus(302);

        $user_id = User::find(1);
        $user = Attendance::where('user_id', $user_id->id)->where('date', $carbon)->first();
        $workEnd = $user->end_time;
        $rest = null;

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('退勤済');
    }

    // 退勤処理後時刻確認
    public function testAttendanceList()
    {
        $this->get('/attendance')->assertStatus(200);
        $this->get('/attendance/start')->assertStatus(302);
        $this->get('/attendance/end')->assertStatus(302);

        $carbon = new CarbonImmutable();
        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');
        $user_id = User::find(1);
        $attendance = Attendance::where('user_id', $user_id->id)->where('date', $carbon)->first();
        $response->assertSee(substr($attendance->endtime, 0, 5));
    }
}
