<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class ModuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Module::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject_id' => Subject::all()->random()->id,
            'title' =>  "Module " . $this->faker->numberBetween(0, 100),
            'duration' => $this->faker->time("H:i"),
            'priority' => random_int(1, 3)
        ];
    }

}
