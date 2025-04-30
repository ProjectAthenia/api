<?php
declare(strict_types=1);

namespace App\Athenia\Models\User;

use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Statistics\Statistic;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class UserStatistic
 *
 * @property int $id
 * @property int $statistic_id
 * @property int $user_id
 * @property int $count
 * @property Carbon|null $deleted_at
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read Statistic $statistic
 * @property-read User $user
 * @mixin Eloquent
 */
class UserStatistic extends BaseModelAbstract
{
    /**
     * The statistic this is tied to
     *
     * @return BelongsTo
     */
    public function statistic(): BelongsTo
    {
        return $this->belongsTo(Statistic::class);
    }

    /**
     * The user this is tied to
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 