<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Carbon\CarbonImmutable;
use App\Services\CsvService;

class CsvServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_csv_format() :void
    {
        $attendances = collect([
            (object)[
                'date' => CarbonImmutable::parse('2026-03-16'),
                'start_time' => CarbonImmutable::parse('08:00'),
                'end_time' => CarbonImmutable::parse('17:00'),
                'total_rest_time' => '01:00:00',
                'total_work_time' => '08:00:00',
            ]
        ]);

        $service = new CsvService;
        $result = $service->format($attendances);

        $expected = "日付,出勤,退勤,休憩,合計\r\n";
        $expected .= "2026/03/16,08:00,17:00,01:00,08:00\r\n";
        $expected = mb_convert_encoding($expected, 'SJIS-win', 'UTF-8');
        $this->assertSame($expected, $result);
    }
}
