<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rest;
use Carbon\CarbonImmutable;

class RestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->get('/login')->assertStatus(200);

        $this->post(url('/login'), ['email' => $this->user->email, 'password' => '11111111']);
        $this->assertAuthenticatedAs($this->user);
        $this->get('/attendance/start')->assertStatus(302);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  休憩開始処理
    public function testRestStart()
    {
        $carbon = new CarbonImmutable();
        $user = User::find(1);
        $workEnd = $user->end_time;
        $rest = null;

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('休憩入');

        $this->get('/rest/start')->assertStatus(302);

        $rest = Rest::find(1);
        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('休憩中');
    }

    // 休憩開始繰り返し可能
    public function testRestStartRepeat()
    {
        $this->get('/rest/start')->assertStatus(302);
        $this->get('/rest/end')->assertStatus(302);

        $rest = Rest::find(1);
        $this->assertDatabaseHas('rests', [
            'id' => 1,
            'attendance_id' => 1,
            'start_time' => $rest->start_time,
            'end_time' => $rest->end_time
        ]);

        $carbon = new CarbonImmutable();
        $user = User::find(1);
        $workEnd = $user->end_time;
        $rest = Rest::where('id', 1)->whereNull('end_time')->exists();

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('休憩入');
    }

    // 休憩終了処理
    public function testRestEnd()
    {
        $this->get('/rest/start')->assertStatus(302);

        $carbon = new CarbonImmutable();
        $user = User::find(1);
        $workEnd = $user->end_time;
        $rest = Rest::where('id', 1)->whereNull('end_time')->exists();

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('休憩戻');

        $this->get('/rest/end')->assertStatus(302);

        $rest = Rest::where('id', 1)->whereNull('end_time')->exists();

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('出勤中');
    }

    // 休憩終了繰り返し可能
    public function testRestEndRepeat()
    {
        $this->get('/rest/start')->assertStatus(302);
        $this->get('/rest/end')->assertStatus(302);

        $rest = Rest::find(1);
        $this->assertDatabaseHas('rests', [
            'id' => 1,
            'attendance_id' => 1,
            'start_time' => $rest->start_time,
            'end_time' => $rest->end_time
        ]);

        $this->get('/rest/start')->assertStatus(302);

        $carbon = new CarbonImmutable();
        $user = User::find(1);
        $workEnd = $user->end_time;
        $rest = Rest::where('id', 2)->whereNull('end_time')->exists();

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('休憩戻');
    }

    // 休憩処理後時刻確認
    public function testAttendanceList()
    {
        $this->get('/rest/start')->assertStatus(302);
        $this->get('/rest/end')->assertStatus(302);
        $rest = Rest::find(1);
        $this->assertDatabaseHas('rests', [
            'id' => 1,
            'attendance_id' => 1,
            'start_time' => $rest->start_time,
            'end_time' => $rest->end_time
        ]);

        $this->get('/attendance/start')->assertStatus(302);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');
        $rest = Rest::find(1);
        $restStart = new CarbonImmutable($rest->start_time);
        $restEnd = new CarbonImmutable($rest->end_time);
        $diffRest = $restStart->diffInSeconds($restEnd);

        $response->assertSee(gmdate('H:i:s', $diffRest));
    }
}
