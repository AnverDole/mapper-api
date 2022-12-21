<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ScheduleSlot;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(1)
        // ->hasSettings(1)
        // ->hasSubjects(10)
        // ->create();

        Subject::factory(10)->create();
        Module::factory(10)->create();
    }
}
