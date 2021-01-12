<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\JobPost;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobPost::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'                   => 1,
            'title'                     => $this->faker->title,
            'required_experience_level' => $this->faker->jobTitle,
            'job_requirements'          => $this->faker->bloodType,
            'start_date'                => Carbon::now(),
            'end_date'                  => Carbon::now()->addWeek(),
        ];
    }
}
