<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Events\Vote\VoteCreatedEvent;
use App\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Vote
 *
 * @property int $id
 * @property int $ballot_item_option_id
 * @property int $ballot_completion_id
 * @property int $result
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Vote\BallotCompletion $ballotCompletion
 * @property-read \App\Models\Vote\BallotItemOption $ballotItemOption
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Vote newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Vote newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Vote query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereBallotCompletionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereBallotItemOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Vote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vote extends BaseModelAbstract
{
    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => VoteCreatedEvent::class,
    ];

    /**
     * The ballot completion that this is part of
     *
     * @return BelongsTo
     */
    public function ballotCompletion(): BelongsTo
    {
        return $this->belongsTo(BallotCompletion::class);
    }

    /**
     * The subject that was voted for
     *
     * @return BelongsTo
     */
    public function ballotItemOption(): BelongsTo
    {
        return $this->belongsTo(BallotItemOption::class);
    }
}
