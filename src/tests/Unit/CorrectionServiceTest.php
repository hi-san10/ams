<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionAttendance;
use App\Models\StampCorrectionRequest;
use App\Services\CorrectionService;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;

class CorrectionServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_correction() :void
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(RestsTableSeeder::class);
        $user = User::find(1);
        $this->actingAs($user);

        $carbon = CarbonImmutable::today();

        $attendance = Attendance::with('rests')
            ->where('user_id', $user->id)
            ->whereYear('date', $carbon)
            ->whereMonth('date', $carbon)
            ->orderBy('date', 'asc')
            ->first();

        $request = new Request([
            'start' => "08:10",
            'end' => "17:10",
            'rests' => [
                [
                    'start_time' => "10:20",
                    'end_time' => "10:30",
                ]
            ],
            'remarks' => "電車遅延",
        ]);
        $service = new CorrectionService;
        $service->correction($request, $attendance);

        $this->assertDatabaseCount('stamp_correction_requests', 1);
        $this->assertDatabaseCount('correction_attendances', 1);
        $this->assertDatabaseCount('correction_rests', 1);

        $this->assertDatabaseHas('stamp_correction_requests', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => "電車遅延",
        ]);

        $stampCorrectionRequest = StampCorrectionRequest::first();
        $this->assertDatabaseHas('correction_attendances', [
            'stamp_correction_request_id' => $stampCorrectionRequest->id,
            'start_time' => $carbon->format("Y-m-d") . " 08:10:00",
            'end_time' => $carbon->format("Y-m-d") . " 17:10:00",
        ]);

        $correctionAttendance = CorrectionAttendance::first();
        $this->assertDatabaseHas('correction_rests', [
            'correction_attendance_id' => $correctionAttendance->id,
            'start_time' => $carbon->format("Y-m-d") . " 10:20:00",
            'end_time' => $carbon->format("Y-m-d") . " 10:30:00",
        ]);
    }
}
