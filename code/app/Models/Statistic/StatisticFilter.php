<?php
declare(strict_types=1);

namespace App\Models\Statistic;

use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StatisticFilter
 * @package App\Models\Statistics
 *
 * @property int $id
 * @property int $statistic_id
 * @property string $field
 * @property string $operator
 * @property string|null $value
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read Statistic $statistic
 */
class StatisticFilter extends BaseModelAbstract
{
    /**
     * The statistic that this filter belongs to
     *
     * @return BelongsTo
     */
    public function statistic(): BelongsTo
    {
        return $this->belongsTo(Statistic::class);
    }
} 