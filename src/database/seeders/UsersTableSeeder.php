<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テスト用ユーザー
        $content = [
            'name' => 'mm',
            'email' => 'm@mm',
            'password' => Hash::make('00000000'),
            'email_verified_at' => CarbonImmutable::today()
        ];

        DB::table('users')->insert($content);

        $content = [
            'name' => '佐藤',
            'email' => 'sato@mail.com',
            'password' => Hash::make('99999999'),
            'email_verified_at' => CarbonImmutable::today()
        ];

        DB::table('users')->insert($content);
    }
}
