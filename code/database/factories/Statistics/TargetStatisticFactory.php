<?php
declare(strict_types=1);

namespace Database\Factories\Statistics;

use App\Models\Statistics\TargetStatistic;
use App\Models\Statistics\Statistic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TargetStatisticFactory
 */
class TargetStatisticFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TargetStatistic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'target_id' => User::factory(),
            'target_type' => User::class,
            'statistic_id' => Statistic::factory(),
            'value' => $this->faker->randomFloat(2, 0, 1000),
            'filters' => null,
        ];
    }

    /**
     * Configure the factory to use a specific target model.
     *
     * @param mixed $model The model instance or factory
     * @param string $type The class name of the target type
     * @return Factory
     */
    public function forTarget(mixed $model, string $type): Factory
    {
        return $this->state(function (array $attributes) use ($model, $type) {
            return [
                'target_id' => $model,
                'target_type' => $type,
            ];
        });
    }
} 