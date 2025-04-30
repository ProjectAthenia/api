<?php
declare(strict_types=1);

namespace Database\Factories\Statistics;

use App\Athenia\Models\Statistics\Statistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class StatisticFactory
 * @package Database\Factories\Statistics
 */
class StatisticFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Statistic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['character', 'word', 'radical']),
            'public' => $this->faker->boolean,
        ];
    }
} 