<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\HasPolicyContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Resource
 *
 * @property int $id
 * @property string $content
 * @property int $resource_id
 * @property string $resource_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $resource
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Resource newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Resource newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Resource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Resource extends BaseModelAbstract implements HasPolicyContract
{
    /**
     * The database resource this is related to
     *
     * @return MorphTo
     */
    public function resource() : MorphTo
    {
        return $this->morphTo();
    }
}
