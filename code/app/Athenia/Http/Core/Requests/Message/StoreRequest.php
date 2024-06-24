<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Message;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Category;
use App\Models\Messaging\Message;
use App\Policies\CategoryPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Category
 */
class StoreRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Message $message
     * @return array
     */
    public function rules(Message $message)
    {
        return $message->getValidationRules(Message::VALIDATION_RULES_CREATE);
    }
}