<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Contracts\Models\BelongsToOrganizationContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Role;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class OrganizationManager
 *
 * @property int $id
 * @property int $user_id
 * @property int $organization_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Organization\Organization $organization
 * @property-read \App\Models\Role $role
 * @property-read \App\Models\User\User $user
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\OrganizationManager newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\OrganizationManager newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\OrganizationManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\OrganizationManager whereUserId($value)
 * @mixin \Eloquent
 */
class OrganizationManager extends BaseModelAbstract implements HasValidationRulesContract, BelongsToOrganizationContract
{
    use HasValidationRules, BelongsToOrganization;

    /**
     * The related organization
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The related user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
                'role_id' => [
                    'required',
                    'integer',
                    Rule::in(Role::ENTITY_ROLES),
                ],
                'email' => [
                    'string',
                    'email',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'email',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'email',
                ],
            ],
        ];
    }
}