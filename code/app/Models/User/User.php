<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\CanBeIndexedContractContract;
use App\Contracts\Models\HasPolicyContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Contracts\Models\IsAnEntityContract;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Resource;
use App\Models\Role;
use App\Models\Traits\CanBeIndexed;
use App\Models\Traits\HasValidationRules;
use App\Models\Traits\IsEntity;
use App\Models\Vote\BallotCompletion;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property int $id
 * @property int|null $merged_to_id
 * @property string|null $stripe_customer_key
 * @property string $email
 * @property string|null $first_name
 * @property string $password
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $allow_users_to_add_me
 * @property bool $receive_push_notifications
 * @property string|null $about_me
 * @property string|null $push_notification_key
 * @property int|null $profile_image_id
 * @property string|null $last_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Asset[] $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\BallotCompletion[] $ballotCompletions
 * @property-read int|null $ballot_completions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wiki\Article[] $createdArticles
 * @property-read int|null $created_articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wiki\ArticleIteration[] $createdIterations
 * @property-read int|null $created_iterations_count
 * @property-read null|string $profile_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Messaging\Message[] $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organization\OrganizationManager[] $organizationManagers
 * @property-read int|null $organization_managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\PaymentMethod[] $paymentMethods
 * @property-read int|null $payment_methods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User\ProfileImage|null $profileImage
 * @property-read \App\Models\Resource|null $resource
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Messaging\Thread[] $threads
 * @property-read int|null $threads_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\User newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\User newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereAboutMe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereAllowUsersToAddMe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereMergedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereProfileImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User wherePushNotificationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereReceivePushNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereStripeCustomerKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends BaseModelAbstract
    implements AuthenticatableContract, JWTSubject,
            HasPolicyContract, HasValidationRulesContract,
            CanBeIndexedContractContract, IsAnEntityContract
{
    use Authenticatable, HasValidationRules, IsEntity, CanBeIndexed;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'password',
    ];

    /**
     * The url of the profile image
     *
     * @var array
     */
    protected $appends = [
        'profile_image_url',
    ];

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
     * The ballot completions the user has done
     *
     * @return HasMany
     */
    public function ballotCompletions(): HasMany
    {
        return $this->hasMany(BallotCompletion::class);
    }

    /**
     * The articles that were created by this user
     *
     * @return HasMany
     */
    public function createdArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by_id');
    }

    /**
     * The iterations that were created by this user
     *
     * @return HasMany
     */
    public function createdIterations(): HasMany
    {
        return $this->hasMany(ArticleIteration::class, 'created_by_id');
    }

    /**
     * The messages that were sent to a user
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_id');
    }

    /**
     * All organization manager relations this user has
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
     * The resource object for this user
     *
     * @return MorphOne
     */
    public function resource() : MorphOne
    {
        return $this->morphOne(Resource::class, 'resource');
    }

    /**
     * What roles this user has
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Any threads this user is apart of
     *
     * @return BelongsToMany
     */
    public function threads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class);
    }

    /**
     * Add a Role to this user
     *
     * @param int $roleId
     * @return $this
     */
    public function addRole(int $roleId)
    {
        $this->roles()->attach($roleId);
        return $this;
    }

    /**
     * Does this have the role
     *
     * @param mixed $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        $roles = (array)$roles;
        return $this->roles()->whereIn('id', $roles)->exists();
    }

    /**
     * Add a Role to this user
     *
     * @param int $roleId
     * @return $this
     */
    public function removeRole(int $roleId)
    {
        $this->roles()->detach($roleId);
        return $this;
    }

    /**
     * Determines whether or not the user can manage the organization.
     *
     * @param Organization $organization
     * @param int|array $role
     *          If the manager role is passed in then this will return true for both the manager role and admin role.
     *          The admin role will only check for the admin role.
     * @return bool
     */
    public function canManageOrganization(Organization $organization, $role = Role::MANAGER): bool
    {
        $roles = is_array($role) ? $role : [$role];
        if (!in_array(Role::ADMINISTRATOR, $roles)) {
            $roles[] = Role::ADMINISTRATOR;
        }
        return $this->organizationManagers->first(fn (OrganizationManager $organizationManager) =>
            in_array($organizationManager->role_id, $roles) && $organizationManager->organization_id === $organization->id
        ) != null;
    }

    /**
     * Get the URL for the profile image
     *
     * @return null|string
     */
    public function getProfileImageUrlAttribute(): string|null
    {
        return $this->profileImage?->url;
    }

    /**
     * @inheritDoc
     */
    public function canUserManageEntity(User $user, int $role = null): bool
    {
        return $this->id == $user->id;
    }

    /**
     * The name of the morph relation
     *
     * @return string
     */
    public function morphRelationName(): string
    {
        return 'user';
    }

    /**
     * Gets the content string to index
     *
     * @return string
     */
    public function getContentString(): ?string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        $emailUnique = Rule::unique('users', 'email');

        $userId = count($params) ? $params[0]->id : null;

        if ($userId) {
            $emailUnique->ignore($userId);
        }

        return [
            static::VALIDATION_RULES_BASE => [
                'email' => [
                    'string',
                    'max:120',
                    'email',
                    $emailUnique,
                ],
                'first_name' => [
                    'string',
                    'max:120',
                ],
                'last_name' => [
                    'string',
                    'max:120',
                ],
                'password' => [
                    'string',
                    'min:6',
                ],
                'push_notification_key' => [
                    'string',
                    'max:512'
                ],
                'about_me' => [
                    'string',
                ],
                'allow_users_to_add_me' => [
                    'boolean',
                ],
                'receive_push_notifications' => [
                    'boolean',
                ],
            ],
        ];
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="User",
     *     @SWG\Property(
     *         property="id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the model",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was created",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was last updated",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="email",
     *         type="string",
     *         maxLength=120,
     *         description="The email address of this user"
     *     ),
     *     @SWG\Property(
     *         property="name",
     *         type="string",
     *         maxLength=120,
     *         description="The name of this user"
     *     ),
     *     @SWG\Property(
     *         property="password",
     *         type="string",
     *         minLength=6,
     *         description="The password for this user. This cannot be read, and it can only be set."
     *     ),
     *     @SWG\Property(
     *         property="roles",
     *         description="The roles that this user has.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Role")
     *     ),
     *     @SWG\Property(
     *         property="created_articles",
     *         description="The articles that this user created.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Articles")
     *     ),
     *     @SWG\Property(
     *         property="created_iterations",
     *         description="The iterations that this user created.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Iterations")
     *     )
     * )
     */
}
