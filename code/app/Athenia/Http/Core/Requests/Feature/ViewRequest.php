<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Feature;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Feature
 */
class ViewRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;
}
