<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Statement;

class StatementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Statement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $base_date = Carbon::createMidnightDate($this->faker->dateTimeBetween('-1 years'));

        $date_sent = $date_abolished = $base_date->format('Y-m-d G:i:s');

        $user_id = User::all()->random()->id;

        return [

            'decision_taken' => $this->faker->randomElement(array_keys(Statement::DECISIONS)),
            'decision_ground' => $this->faker->randomElement(array_keys(Statement::DECISION_GROUNDS)),
            'category' => $this->faker->randomElement(array_keys(Statement::SOR_CATEGORIES)),

            'illegal_content_legal_ground' => $this->faker->text(100),
            'illegal_content_explanation' => $this->faker->realText(500),
            'incompatible_content_ground' => $this->faker->text(100),
            'incompatible_content_explanation' => $this->faker->realText(500),

            'url' => $this->faker->url,

            'countries_list' => $this->faker->randomElements(Statement::EUROPEAN_COUNTRY_CODES, rand(1, 8)),

            'date_abolished' => $date_abolished,

            'source' => $this->faker->randomElement(array_keys(Statement::SOURCES)),
            'source_explanation' => $this->faker->realText(500),

            'automated_detection' => $this->faker->randomElement(Statement::AUTOMATED_DETECTIONS),
            'automated_decision' => $this->faker->randomElement(Statement::AUTOMATED_DECISIONS),
            'automated_takedown' => $this->faker->randomElement(Statement::AUTOMATED_TAKEDOWNS),


            'user_id' => $user_id,
            'method' => $this->faker->randomElement([Statement::METHOD_API, Statement::METHOD_FORM]),
            'created_at' => $date_sent
        ];
    }
}
