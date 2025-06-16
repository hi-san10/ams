<?php

namespace Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class WorkTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post(url('/login'), ['email' => $this->user->email, 'password' => '11111111']);
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  出勤処理
    public function testAtWork()
    {
        $response = $this->get('/attendance')->assertStatus(200);
        $response->assertSee('出勤');

        $this->get('/attendance/start');

        $carbon = new CarbonImmutable();
        $user = $this->assertDatabaseHas('attendances', [
            'id' => 1,
            'user_id' => $this->user->id,
            'date' => $carbon,
            'start_time' => $carbon,
            'end_time' => null,
            'created_at' => $carbon,
            'updated_at' => $carbon
        ]);
        $workEnd = null;
        $rest = null;

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee('出勤中');
    }

    // 退勤済みは出勤ボタン非表示
    public function testWorkEnd()
    {
        $this->get('/attendance');

        $this->get('/attendance/start')->assertStatus(302);
        $this->get('/attendance/end')->assertStatus(302);

        $carbon = new CarbonImmutable();
        $user = $this->assertDatabaseHas('attendances', [
            'id' => 1,
            'user_id' => $this->user->id,
            'date' => $carbon,
            'start_time' => $carbon,
            'end_time' => $carbon,
            'created_at' => $carbon,
            'updated_at' => $carbon
        ]);
        $workEnd = $carbon;
        $rest = null;

        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertDontSee('<a href="/attendance/start" class="attendance_link">出勤</a>');
    }

    // 出勤処理後時刻確認
    public function testAttendanceList()
    {
        $this->get('/attendance')->assertStatus(200);

        $this->get('/attendance/start')->assertStatus(302);

        $response = $this->get('/attendance/list')->assertViewIs('attendances.list');
        $user = User::find(1);
        $response->assertSee($user->start_time);
    }
}
