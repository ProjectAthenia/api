<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class BallotItemOption
 *
 * @property int $id
 * @property int $ballot_item_id
 * @property int $vote_count
 * @property int $subject_id
 * @property string $subject_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Vote\BallotItem $ballotItem
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\Vote[] $votes
 * @property-read int|null $votes_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\BallotItemOption newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\BallotItemOption newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\BallotItemOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereBallotItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotItemOption whereVoteCount($value)
 * @mixin \Eloquent
 */
class BallotItemOption extends BaseModelAbstract
{
    /**
     * @return BelongsTo
     */
    public function ballotItem()
    {
        return $this->belongsTo(BallotItem::class);
    }

    /**
     * The subject that this represents
     *
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The votes that have been submitted where this option was selected
     *
     * @return HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
