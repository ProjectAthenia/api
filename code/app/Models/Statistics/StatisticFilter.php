<?php
declare(strict_types=1);

namespace App\Athenia\Models\Statistics;

use App\Athenia\Models\BaseModelAbstract;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class StatisticFilter
 *
 * @property int $id
 * @property int $statistic_id
 * @property string $field
 * @property string $operator
 * @property string $value
 * @property Carbon|null $deleted_at
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read Statistic $statistic
 * @mixin Eloquent
 */
class StatisticFilter extends BaseModelAbstract
{
    /**
     * The statistic this belongs to
     *
     * @return BelongsTo
     */
    public function statistic(): BelongsTo
    {
        return $this->belongsTo(Statistic::class);
    }
} 