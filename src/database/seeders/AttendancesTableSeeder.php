<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start = CarbonImmutable::now()->subMonthNoOverflow()->startOfMonth();
        $end = $start->endOfMonth();

        $date = $start;
        while ($date->lte($end)) {
            if (!$date->isWeekend()) {
                DB::table('attendances')->insert([
                    'user_id' => 2,
                    'date' => $date->toDateString(),
                    'start_time' => '08:00',
                    'end_time' => '17:00',
                ]);
            }
            $date = $date->addDay();
        }
    }
}
