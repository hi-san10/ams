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
        $carbon = new CarbonImmutable();

        $content = [
            'user_id' => '1',
            'date' => $carbon,
            'start_time' => '08:00',
            'end_time' => '17:00'
        ];

        DB::table('attendances')->insert($content);

        $content = [
            'user_id' => '1',
            'date' => $carbon->subMonthNoOverflow(1),
            'start_time' => '08:00',
            'end_time' => '17:00'
        ];

        DB::table('attendances')->insert($content);

        $content = [
            'user_id' => '1',
            'date' => $carbon->addMonthNoOverflow(1),
            'start_time' => '08:00',
            'end_time' => '17:00'
        ];

        DB::table('attendances')->insert($content);
    }
}
