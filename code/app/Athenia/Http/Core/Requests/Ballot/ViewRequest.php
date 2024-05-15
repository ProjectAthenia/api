<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Ballot;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Vote\Ballot;
use App\Policies\Vote\BallotPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Ballot
 */
class ViewRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return BallotPolicy::ACTION_VIEW;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Ballot::class;
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

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'ballotItems',
            'ballotItems.ballotItemOptions',
            'ballotItems.ballotItemOptions.subject',
        ];
    }
}
