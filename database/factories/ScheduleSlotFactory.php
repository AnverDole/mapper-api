<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\ScheduleSlot;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScheduleSlot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = Carbon::now()->subMinutes(rand(1, 55));
        $end = (clone $start)->addMinutes(rand(60, 120));

        $duration = $end->diffInMinutes($end);

        return [
            'user_id' => User::all()->random()->id,
            'module_id' => Module::all()->random()->id,
            'start_at' => $start,
            'duration' => $duration,
            'end_at' => $end,
            'is_finished' => rand(0, 1)
        ];
    }
}
