<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;


class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_attendance_list()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $user = User::find(1);

        $carbon = CarbonImmutable::today();

        $service = new AttendanceService;
        $result = $service->attendanceList($user->id, $carbon);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(1, $result);

        $attendance = $result->first();
        $this->assertSame("08:00", $attendance->start_time);
        $this->assertSame("17:00", $attendance->end_time);
        $this->assertSame("00:10:00", $attendance->totalRest);
        $this->assertSame("08:50:00", $attendance->totalWork);
    }
}
