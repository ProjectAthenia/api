<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Athenia\Models\BaseModelAbstract;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Ballot
 *
 * @property int $id
 * @property string|null $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\BallotCompletion[] $ballotCompletions
 * @property-read int|null $ballot_completions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\BallotItem[] $ballotItems
 * @property-read int|null $ballot_items_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Ballot newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Ballot newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Ballot query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ballot extends BaseModelAbstract
{
    /**
     * This ballot type when there is a single subject and the user chooses yes or no
     */
    const TYPE_SINGLE_OPTION = 'single_option';

    /**
     * The ballot type for when there will be multiple options within each item
     */
    const TYPE_MULTIPLE_OPTIONS = 'multiple_options';

    /**
     * The ballot type for when we allow the user to rank their options
     */
    const TYPE_RANKED_CHOICE = 'ranked_choice';

    /**
     * All times someone has completed this ballot
     *
     * @return HasMany
     */
    public function ballotCompletions(): HasMany
    {
        return $this->hasMany(BallotCompletion::class);
    }

    /**
     * All subjects contained in this ballot
     *
     * @return HasMany
     */
    public function ballotItems(): HasMany
    {
        return $this->hasMany(BallotItem::class);
    }
}
