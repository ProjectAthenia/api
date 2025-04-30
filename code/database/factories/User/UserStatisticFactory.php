<?php
declare(strict_types=1);

namespace Database\Factories\User;

use App\Athenia\Models\Statistics\Statistic;
use App\Athenia\Models\User\User;
use App\Athenia\Models\User\UserStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class UserStatisticFactory
 * @package Database\Factories\User
 */
class UserStatisticFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserStatistic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'statistic_id' => Statistic::factory()->create()->id,
            'filters' => null,
        ];
    }
} 