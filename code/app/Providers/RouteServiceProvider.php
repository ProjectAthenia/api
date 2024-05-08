<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\Category;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Feature;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Payment\PaymentMethod;
use App\Models\Role;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Models\Vote\Ballot;
use App\Models\Vote\BallotCompletion;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use App\Providers\AtheniaProviders\BaseRouteServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 * @package App\Providers
 */
class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * Gets all application specific model placeholders
     *
     * @return array
     */
    public function getAppModelPlaceholders(): array
    {
        return [
        ];
    }
}
