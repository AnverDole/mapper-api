<?php

namespace Database\Factories;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::all()->random()->id,
            "prioritization" => array_keys(Settings::$priorityCategories)[random_int(0, count(Settings::$priorityCategories) - 1)],
            "duration_between_activities" => random_int(10, 45),
            "activity_max_duration" => random_int(45, 60),
            "day_starts_at" => "08:00",
            "day_ends_at" => "18:00"
        ];
    }
}
