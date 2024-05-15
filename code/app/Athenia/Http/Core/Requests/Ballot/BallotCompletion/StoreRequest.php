<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Ballot\BallotCompletion;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Vote\BallotCompletion;
use App\Policies\Vote\BallotCompletionPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Ballot\BallotCompletion
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return BallotCompletionPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return BallotCompletion::class;
    }

    /**
     * Get validation rules for the create request
     *
     * @param BallotCompletion $ballotCompletion
     * @return array
     */
    public function rules(BallotCompletion $ballotCompletion) : array
    {
        return $ballotCompletion->getValidationRules(BallotCompletion::VALIDATION_RULES_CREATE);
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('ballot'),
        ];
    }
}
