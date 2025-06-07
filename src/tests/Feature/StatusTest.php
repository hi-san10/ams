<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\CarbonImmutable;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        $this->carbon = new CarbonImmutable();

        $this->user = User::factory()->create();
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post(url('/login'), ['email' => $this->user->email, 'password' => '11111111']);
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * A basic feature test example.
     *
     * @dataProvider statusProvider
     * @return void
     */
    public function testStatus($user, $rest, $workEnd, $result)
    {
        $carbon = new CarbonImmutable();
        $view = $this->view('attendances.attendance', ['carbon' => $carbon, 'user' => $user, 'workEnd' => $workEnd,  'rest' => $rest]);
        $view->assertSee($result);
    }

    public function statusProvider(): array
    {
        return [
            '出勤中' => [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'date' => new CarbonImmutable(),
                    'start_time' => new CarbonImmutable(),
                    'end_time' => null
                ],
                null,
                null,
                '出勤中'
            ],

            '勤務外' => [
                null,
                null,
                null,
                '勤務外'
            ],

            '休憩中' => [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'date' => new CarbonImmutable(),
                    'start_time' => new CarbonImmutable(),
                    'end_time' => null
                ],
                [
                    'id' => 1,
                    'attendance_id' => 1,
                    'start_time' => new CarbonImmutable(),
                    'end_time' => null
                ],
                null,
                '休憩中'
            ],

            '退勤済み' => [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'date' => new CarbonImmutable(),
                    'start_time' => new CarbonImmutable(),
                    'end_time' => new CarbonImmutable()
                ],
                [],
                [
                    new CarbonImmutable()
                ],
                '退勤済'
            ]
        ];
    }
}
