<?php
declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use App\Validators\ArticleVersion\SelectedIterationBelongsToArticleValidator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

/**
 * Class ArticleVersion
 *
 * @property int $id
 * @property int $article_id
 * @property int $iteration_id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Wiki\Article $article
 * @property-read \App\Models\Wiki\ArticleIteration $articleIteration
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Wiki\ArticleVersion newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Wiki\ArticleVersion newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Wiki\ArticleVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereIterationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\ArticleVersion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleVersion extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * The article this version is for
     *
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * The iteration that this version is for
     *
     * @return BelongsTo
     */
    public function articleIteration(): BelongsTo
    {
        return $this->belongsTo(ArticleIteration::class);
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        $articleIterationProps = [
            'bail',
            'required',
            'int',
            Rule::exists('iterations', 'id'),
            SelectedIterationBelongsToArticleValidator::KEY,
        ];
        return [
            static::VALIDATION_RULES_BASE => [
                'article_iteration_id' => $articleIterationProps,
                'iteration_id' => $articleIterationProps,
            ],
        ];
    }
}
