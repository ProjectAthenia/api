<?php
declare(strict_types=1);

namespace Database\Factories\Statistics;

use App\Athenia\Models\Statistics\Statistic;
use App\Athenia\Models\Statistics\StatisticFilter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class StatisticFilterFactory
 * @package Database\Factories\Statistics
 */
class StatisticFilterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StatisticFilter::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'statistic_id' => Statistic::factory()->create()->id,
            'field' => $this->faker->word,
            'operator' => $this->faker->randomElement(['=', '>', '<', '>=', '<=', '!=']),
            'value' => $this->faker->word,
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'type' => $this->faker->word,
            'options' => null,
        ];
    }
} 