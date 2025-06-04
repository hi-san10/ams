<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testGetDate()
    {
        $user = User::factory()->create();
        $this->get('/login');
        $response = $this->post(url('/login'), ['email' => $user->email, 'password' => '11111111']);
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
