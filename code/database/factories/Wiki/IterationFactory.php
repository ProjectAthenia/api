<?php
declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\Iteration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class IterationFactory
 * @package Database\Factories\Wiki
 */
class IterationFactory extends Factory
{
    /**
     * @var string The related model
     */
    protected $model = Iteration::class;

    /**
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->text,
            'article_id' => Article::factory()->create()->id,
            'created_by_id' => User::factory()->create()->id,
        ];
    }
}
