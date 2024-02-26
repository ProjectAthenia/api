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
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 * @package App\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    private $modelPlaceHolders = [
        'article' => Article::class,
        'article_iteration' => ArticleIteration::class,
        'ballot' => Ballot::class,
        'ballot_completion' => BallotCompletion::class,
        'category' => Category::class,
        'collection' => Collection::class,
        'collection_item' => CollectionItem::class,
        'feature' => Feature::class,
        'membership_plan' => MembershipPlan::class,
        'organization' => Organization::class,
        'organization_manager' => OrganizationManager::class,
        'payment_method' => PaymentMethod::class,
        'role' => Role::class,
        'subscription' => Subscription::class,
        'user' => User::class,
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        foreach($this->modelPlaceHolders as $placeHolder => $model) {
            Route::pattern($placeHolder, '^[0-9]+$');
            Route::model($placeHolder, $model);
        }

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        Route::middleware('api-v1')
            ->namespace('App\Http\V1\Controllers')
            ->group(base_path('routes/api-v1.php'));
    }
}
