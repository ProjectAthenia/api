<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

/**
 * Class Contact
 *
 * @property int $id
 * @property int $initiated_by_id
 * @property int $requested_id
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $denied_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User\User $initiatedBy
 * @property-read \App\Models\User\User $requested
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\Contact newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\Contact newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereDeniedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereInitiatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereRequestedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Contact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contact extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * @var array
     */
    protected $casts = [
        'confirmed_at' => 'datetime',
        'denied_at' => 'datetime',
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