<?php
declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BaseModelAbstract
 * @package App\Models
 */
abstract class BaseModelAbstract extends Model
{
    use EloquentJoin;

    /**
     * The default fields that are guarded in all models
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The deleted at field is by default hidden
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * All models have a deleted at along with the standard fields
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
    ];

    /**
     * All our models will be set with a deleted at timestamp
     */
    use SoftDeletes;
}