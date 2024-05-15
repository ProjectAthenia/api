<?php
declare(strict_types=1);

namespace App\Models;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

/**
 * Class Asset
 *
 * @property int $id
 * @property int|null $owner_id
 * @property string|null $name
 * @property string|null $caption
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property string|null $owner_type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Asset newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Asset newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asset whereUrl($value)
 * @mixin \Eloquent
 */
class Asset extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * @var string Makes sure to override all children
     */
    protected $table = 'assets';

    /**
     * The user that created this asset
     *
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * All mime types that can be uploaded to the server for this asset
     *
     * @return array
     */
    protected function getAvailableMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/gif',
        ];
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'file_contents' => [
                    'string',
                ],

                'name' => [
                    'nullable',
                    'string',
                ],

                'caption' => [
                    'nullable',
                    'string',
                ],

                // Set in the request object, and not set from the user request
                'mime_type' => [
                    Rule::in($this->getAvailableMimeTypes()),
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED => [
                    'file_contents',
                ],
            ],
            self::VALIDATION_RULES_UPDATE => [
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'file_contents',
                ],
            ]
        ];
    }
}
