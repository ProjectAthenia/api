<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class Contact
 *
 * @property int $id
 * @property int $initiated_by_id
 * @property int $requested_id
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $denied_at
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User\User $initiatedBy
 * @property-read \App\Models\User\User $requested
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Contact newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Contact newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Contact query()
 * @method static Builder|Contact whereConfirmedAt($value)
 * @method static Builder|Contact whereCreatedAt($value)
 * @method static Builder|Contact whereDeletedAt($value)
 * @method static Builder|Contact whereDeniedAt($value)
 * @method static Builder|Contact whereId($value)
 * @method static Builder|Contact whereInitiatedById($value)
 * @method static Builder|Contact whereRequestedId($value)
 * @method static Builder|Contact whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Contact extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * @var array
     */
    protected $dates = [
        'confirmed_at',
        'denied_at',
    ];

    /**
     * @return BelongsTo
     */
    public function initiatedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by_id');
    }

    /**
     * @return BelongsTo
     */
    public function requested() : BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_id');
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
                'initiated_by_id' => [
                    'not_present',
                ],
                'requested_id' => [
                    'integer',
                    Rule::exists('users', 'id'),
                ],
                'deny' => [
                    'boolean',
                ],
                'confirm' => [
                    'boolean',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'requested_id',
                ],
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'deny',
                    'confirm',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'requested_id',
                ],
            ],
        ];
    }
}