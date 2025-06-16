<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\CarbonImmutable;

class GetDateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  勤怠打刻画面日時確認
    public function testGetDate()
    {
        $user = User::factory()->create();
        $this->get('/login');
        $this->post(url('/login'), ['email' => $user->email, 'password' => '11111111']);

        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);

        $response->assertViewIs('attendances.attendance');
        $carbon = new CarbonImmutable();
        $date = $carbon->isoFormat('YYYY年M月D日(ddd)');
        $time = $carbon->format('H:i');

        $response->assertSee($date);
        $response->assertSee($time);
    }
}
