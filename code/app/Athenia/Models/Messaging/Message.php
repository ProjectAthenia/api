<?php
declare(strict_types=1);

namespace App\Models\Messaging;

use App\Athenia\Contracts\Models\HasPolicyContract;
use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Events\Messaging\MessageCreatedEvent;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\Rule;

/**
 * Class Message
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $subject
 * @property string|null $template
 * @property array $data
 * @property int|null $to_id
 * @property int|null $from_id
 * @property int|null $thread_id
 * @property array|null $via
 * @property string|null $action
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $seen_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $reply_to_email
 * @property string|null $reply_to_name
 * @property-read \App\Models\User\User|null $from
 * @property-read \App\Models\Messaging\Thread|null $thread
 * @property-read \App\Models\User\User|null $to
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\Message newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\Message newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereReplyToEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereReplyToName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\Message whereVia($value)
 * @mixin \Eloquent
 */
class Message extends BaseModelAbstract implements HasPolicyContract, HasValidationRulesContract
{
    use HasValidationRules;

    const VIA_EMAIL = 'email';
    const VIA_SLACK = 'slack';
    const VIA_SMS = 'sms';
    const VIA_PUSH_NOTIFICATION = 'push';

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'via' => 'array',
        'seen_at' => 'datetime',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Array of events that need to be dispatched
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => MessageCreatedEvent::class
    ];

    /**
     * Each message belongs to a user
     *
     * @return MorphTo
     */
    public function from() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The thread that this message is in
     *
     * @return BelongsTo
     */
    public function thread() : BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Each message belongs to a user
     *
     * @return MorphTo
     */
    public function to() : MorphTo
    {
        return $this->morphTo();
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
                'message' => [
                    'string',
                ],
                'seen' => [
                    'boolean',
                ],
                'template' => [
                    Rule::in([
                        'contact',
                    ]),
                ],
                'data' => [
                    'array',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'message',
                ],
            ],
        ];
    }
}