<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = DB::table('attendances')->where('user_id', 2)->get();

        foreach ($attendances as $attendance) {
            DB::table('rests')->insert([
                'attendance_id' => $attendance->id,
                'start_time' => '12:00',
                'end_time' => '13:00',
            ]);

            // 月・水・金は午前休憩も追加
            $dayOfWeek = CarbonImmutable::parse($attendance->date)->dayOfWeek;
            if (in_array($dayOfWeek, [1, 3, 5])) {
                DB::table('rests')->insert([
                    'attendance_id' => $attendance->id,
                    'start_time' => '10:00',
                    'end_time' => '10:15',
                ]);
            }
        }
    }
}
