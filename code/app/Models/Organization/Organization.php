<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Contracts\Models\HasValidationRulesContract;
use App\Contracts\Models\IsAnEntityContract;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\Role;
use App\Models\Traits\IsEntity;
use App\Models\Traits\HasValidationRules;
use App\Models\User\ProfileImage;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Organization
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property int|null $profile_image_id
 * @property string|null $stripe_customer_key
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Asset[] $assets
 * @property-read int|null $assets_count
 * @property-read null|string $profile_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organization\OrganizationManager[] $organizationManagers
 * @property-read int|null $organization_managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\PaymentMethod[] $paymentMethods
 * @property-read int|null $payment_methods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User\ProfileImage|null $profileImage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Organization newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Organization newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereProfileImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereStripeCustomerKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Organization extends BaseModelAbstract
    implements HasValidationRulesContract, IsAnEntityContract
{
    use HasValidationRules, IsEntity;

    /**
     * All assets this user has created
     *
     * @return MorphMany
     */
    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'owner');
    }

    /**
     * All organization managers in this organization
     *
     * @return HasMany
     */
    public function organizationManagers(): HasMany
    {
        return $this->hasMany(OrganizationManager::class);
    }

    /**
     * The asset that contains the profile image for this user
     *
     * @return BelongsTo
     */
    public function profileImage() : BelongsTo
    {
        return $this->belongsTo(ProfileImage::class);
    }

    /**
     * Get the URL for the profile image
     *
     * @return null|string
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profileImage ? $this->profileImage->url : null;
    }

    /**
     * @inheritDoc
     */
    public function morphRelationName(): string
    {
        return 'organization';
    }

    /**
     * @inheritDoc
     */
    public function canUserManageEntity(User $user, int $role = Role::MANAGER): bool
    {
        return $user->canManageOrganization($this, $role);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'name' => [
                    'string',
                    'max:120',
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED => [
                    'name',
                ],
            ],
        ];
    }
}