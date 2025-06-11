<?php

namespace Database\Seeders;

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
        $content = [
            'attendance_id' => 1,
            'start_time' => '10:00',
            'end_time' => '10:10'
        ];

        DB::table('rests')->insert($content);

        $content = [
            'attendance_id' => 2,
            'start_time' => '10:00',
            'end_time' => '10:10'
        ];

        DB::table('rests')->insert($content);

        $content = [
            'attendance_id' => 3,
            'start_time' => '10:00',
            'end_time' => '10:10'
        ];

        DB::table('rests')->insert($content);
    }
}
