<?php
declare(strict_types=1);

namespace App\Models\Statistics;

use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class TargetStatistic
 * @package App\Models\Statistics
 * @property int $id
 * @property int $target_id
 * @property string $target_type
 * @property int $statistic_id
 * @property float $value
 * @property array|null $filters
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\Statistics\Statistic $statistic
 * @property-read \Illuminate\Database\Eloquent\Model $target
 */
class TargetStatistic extends BaseModelAbstract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'target_statistics';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'result' => 'array',
        'value' => 'float',
    ];

    /**
     * The target model that this statistic belongs to
     *
     * @return MorphTo
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The statistic that this belongs to
     *
     * @return BelongsTo
     */
    public function statistic(): BelongsTo
    {
        return $this->belongsTo(Statistic::class);
    }
} 