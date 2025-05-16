<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Rest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminUsersTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        \App\Models\User::factory(10)->create();
        Attendance::factory(50)->create();
        Rest::factory(50)->create();
    }
}