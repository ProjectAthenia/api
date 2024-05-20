<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Models\Messaging\CanReceiveSlackNotificationsContract;
use App\Athenia\Contracts\Models\Messaging\HasMessageReceiversContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Athenia\Models\Traits\IsEntity;
use App\Models\Asset;
use App\Models\Messaging\Message;
use App\Models\Role;
use App\Models\User\ProfileImage;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

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
    implements HasValidationRulesContract, IsAnEntityContract,
        HasMessageReceiversContract, CanReceiveMessageContract, CanReceiveSlackNotificationsContract
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

    /**
     * This will return if the message can be received by the specific model
     *
     * @param Message $message
     * @return bool
     */
    public function canReceiveMessage(Message $message): bool
    {
        if (in_array(Message::VIA_SLACK, $message->via ?? [])) {
            return $this->getSlackChannel($message) && $this->getSlackKey($message);
        }

        return false;
    }

    /**
     * All message receivers contained within this model
     * These related models will be used to send messages when the parent does not
     *
     * @param Message $message The message being sent in case there is only
     *              logic connected to returning receivers
     * @return Collection<CanReceiveMessageContract>
     */
    public function messageReceivers(Message $message): Collection
    {
        return $this->organizationManagers
            ->map(fn (OrganizationManager $i) => $i->user);
    }

    /**
     * Gets the key used to validate access to the related slack workspace
     *
     * @param Message $message
     * @return string|null
     */
    public function getSlackKey(Message $message): ?string
    {
        return $this->slack_key ?? null;
    }

    /**
     * Gets the slack channel name based on the message passed in
     *
     * @param Message $message
     * @return string|null
     */
    public function getSlackChannel(Message $message): ?string
    {
        return $this->slack_channel ?? null;
    }
}