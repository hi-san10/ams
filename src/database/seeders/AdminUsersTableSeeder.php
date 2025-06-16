<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = [
            'name' => '管理者',
            'email' => 'admin@mail.com',
            'password' => Hash::make('00000000'),
        ];

        DB::table('admin_users')->insert($content);
    }
}
