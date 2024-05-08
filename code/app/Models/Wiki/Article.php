<?php
declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\CanBeIndexedContractContract;
use App\Contracts\Models\HasPolicyContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Traits\CanBeIndexed;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Article
 *
 * @property int $id
 * @property int $created_by_id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User\User $createdBy
 * @property-read null|string $content
 * @property-read null|ArticleVersion $current_version
 * @property-read null|string $last_iteration_content
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wiki\ArticleIteration[] $iterations
 * @property-read int|null $iterations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wiki\ArticleVersion[] $versions
 * @property-read int|null $versions_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Wiki\Article newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Wiki\Article newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Wiki\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\Article whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wiki\Article whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Article extends BaseModelAbstract implements HasPolicyContract, HasValidationRulesContract, CanBeIndexedContractContract
{
    use HasValidationRules, CanBeIndexed;

    /**
     * Values that are appending on a toArray function call
     *
     * @var array
     */
    protected $appends = [
        'content',
        'last_iteration_content',
    ];

    /**
     * The user that originally created this article
     *
     * @return BelongsTo
     */
    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * All of the iterations
     *
     * @return HasMany
     */
    public function iterations() : HasMany
    {
        return $this->hasMany(ArticleIteration::class)
            ->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * All modifications for this article
     *
     * @return HasMany
     */
    public function modifications() : HasMany
    {
        return $this->hasMany(ArticleModification::class)
            ->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * All versions related to this article
     *
     * @return HasMany
     */
    public function versions() : HasMany
    {
        return $this->hasMany(ArticleVersion::class)
            ->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * Gets the content of the article
     *
     * @return null|string
     */
    public function getContentAttribute() : ?string
    {
        return $this->current_version?->articleIteration?->content;
    }

    /**
     * Gets the content of the article
     *
     * @return null|ArticleVersion
     */
    public function getCurrentVersionAttribute() : ?ArticleVersion
    {
        return $this->versions()->limit(1)->get()->first();
    }

    /**
     * Gets the content of the article
     *
     * @return null|string
     */
    public function getLastIterationContentAttribute() : ?string
    {
        if (isset($this->attributes['last_iteration_content'])) {
            return $this->attributes['last_iteration_content'];
        }
        /** @var ArticleIteration|null $iteration */
        $iteration = $this->iterations()->limit(1)->get()->first();
        return $iteration ? $iteration->content : null;
    }

    /**
     * @return string
     */
    public function morphRelationName(): string
    {
        return 'article';
    }

    /**
     * Gets the content that will be indexed for this resource
     *
     * @return string|null
     */
    public function getContentString(): ?string
    {
        return $this->title . ' ' . ($this->content ?? '');
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
                'title' => [
                    'string',
                    'max:120',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'title',
                ],
            ],
        ];
    }
}
