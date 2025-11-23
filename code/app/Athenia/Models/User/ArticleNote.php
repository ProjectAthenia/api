<?php
declare(strict_types=1);

namespace App\Athenia\Models\User;

use App\Athenia\Contracts\Models\CanBeAggregatedContract;
use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

/**
 * Class ArticleNote
 *
 * @property int $id
 * @property int $user_id
 * @property int $article_id
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property string|null $response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User\User $user
 * @property-read \App\Models\Wiki\Article $article
 * @mixin \Eloquent
 */
class ArticleNote extends BaseModelAbstract implements HasValidationRulesContract, CanBeAggregatedContract
{
    use HasValidationRules, SoftDeletes;

    /**
     * The user who created this note
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The article this note is for
     *
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'article_id' => [
                    'integer',
                    Rule::exists('articles', 'id'),
                ],
                'completed' => [
                    'boolean',
                ],
                'response' => [
                    'nullable',
                    'string',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'article_id',
                ],
            ],
        ];
    }

    /**
     * Returns the relation paths to the models that can be target statistics
     * For example: ["article"] would mean this model affects statistics on articles
     * through the article relation
     *
     * @return string[]
     */
    public function getStatisticTargetRelationPath(): array
    {
        return ['article'];
    }
}
