<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

/**
 * Class BallotCompletion
 *
 * @property int $id
 * @property int $ballot_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Vote\Ballot $ballot
 * @property-read \App\Models\User\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\Vote[] $votes
 * @property-read int|null $votes_count
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\BallotCompletion newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\BallotCompletion newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\BallotCompletion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotCompletion whereBallotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotCompletion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotCompletion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotCompletion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotCompletion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\BallotCompletion whereUserId($value)
 * @mixin \Eloquent
 */
class BallotCompletion extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * The ballot that was completed
     *
     * @return BelongsTo
     */
    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    /**
     * THe user that completed this ballot
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All votes that were cat in this ballot
     *
     * @return HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
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
                'votes' => [
                    'array',
                ],
                'votes.*' => [
                    'array',
                ],
                'votes.*.result' => [
                    'required',
                    'number',
                ],
                'votes.*.ballot_item_option_id' => [
                    'required',
                    'number',
                    Rule::exists('ballot_item_options', 'id'),
                ],
            ]
        ];
    }
}
