<?php
/**
 * Routes that are available to the public
 */
Route::group(['middleware' => 'jwt.auth.unprotected'], function() {

    Route::get('status', 'StatusController')
        ->name('status');

    /**
     * Categories context
     */
    Route::resource('categories', 'CategoryController', [
        'only' => [
            'index', 'show'
        ]
    ]);

    /**
     * Features Context
     */
    Route::resource('features', 'FeatureController', [
        'only' => [
            'index', 'show',
        ]
    ]);

    /**
     * Categories context
     */
    Route::resource('messages', 'MessageController', [
        'only' => [
            'store',
        ]
    ]);
});

/**
 * Forgot password routes
 */
Route::post('forgot-password', 'ForgotPasswordController@forgotPassword')
    ->name('forgot-password');

Route::post('reset-password', 'ForgotPasswordController@resetPassword')
    ->name('reset-password');

/**
 * Authentication routes
 */
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {

    Route::post('refresh', 'AuthenticationController@refresh')
        ->name('refresh');

    Route::post('login', 'AuthenticationController@login')
        ->name('login');

    Route::post('logout', 'AuthenticationController@logout')
        ->name('logout');

    Route::post('sign-up', 'AuthenticationController@signUp')
        ->name('sign-up');
});

/**
 * Routes that a user needs to be authenticated for in order to access
 */
Route::group(['middleware' => 'jwt.auth.protected'], function() {

    /**
     * Article Context
     */
    Route::resource('articles', 'ArticleController', [
        'except' => [
            'create', 'edit', 'destroy'
        ]
    ]);
    Route::group(['prefix' => 'articles/{article}', 'as' => 'article.'], function () {
        Route::resource('iterations', 'Article\IterationController', [
            'parameters' => [
                'iterations' => 'article_iteration',
            ],
            'only' => [
                'index',
            ],
        ]);

        Route::resource('versions', 'Article\ArticleVersionController', [
            'only' => [
                'index', 'store',
            ],
        ]);
    });

    Route::resource('ballots', 'BallotController', [
        'only' => [
            'show',
        ]
    ]);
    Route::group(['prefix' => 'ballots/{ballot}', 'as' => 'ballot.'], function () {
        Route::resource('ballot-completions', 'Ballot\BallotCompletionController', [
            'only' => [
                'store',
            ],
        ]);
    });
    /**
     * Categories context
     */
    Route::resource('categories', 'CategoryController', [
        'only' => [
            'store', 'update', 'destroy',
        ]
    ]);
    /**
     * Collection context
     */
    Route::resource('collections', 'CollectionController', [
        'only' => [
            'show', 'update', 'destroy',
        ]
    ]);
    Route::group(['prefix' => 'collections/{collection}', 'as' => 'collection.'], function () {

        Route::resource('items', 'Collection\CollectionItemController', [
            'only' => [
                'index', 'store',
            ],
        ]);
    });

    /**
     * Collection Item context
     */
    Route::resource('collection-items', 'CollectionItemController', [
        'only' => [
            'show', 'destroy',
        ]
    ]);

    /**
     * Resource Context
     */
    Route::resource('resources', 'ResourceController', [
        'only' => [
            'index',
        ],
    ]);

    /**
     * User Context
     */
    Route::get('users/me', 'UserController@me')
        ->name('view-self');

    Route::resource('users', 'UserController', [
        'only' => [
            'show', 'update',
        ],
    ]);
    Route::group(['prefix' => 'users/{user}', 'as' => 'user.'], function () {
        require 'entity-routes.php';

        Route::resource('ballot-completions', 'User\BallotCompletionController', [
            'only' => [
                'index',
            ],
        ]);

        Route::resource('contacts', 'User\ContactController', [
            'only' => [
                'index', 'store', 'update',
            ],
        ]);

        Route::resource('threads', 'User\ThreadController', [
            'only' => [
                'index', 'store',
            ],
        ]);

        Route::group(['prefix' => 'threads/{thread}', 'as' => 'thread.'], function () {
            Route::resource('messages', 'User\Thread\MessageController', [
                'only' => [
                    'index', 'store', 'update',
                ],
            ]);
        });
    });

    /**
     * Membership Plan Context
     */
    Route::resource('membership-plans', 'MembershipPlanController', [
        'except' => [
            'create', 'edit'
        ]
    ]);
    Route::group(['prefix' => 'membership-plans/{membership_plan}', 'as' => 'membership-plan.'], function () {
        Route::resource('rates', 'MembershipPlan\MembershipPlanRateController', [
            'only' => [
                'index',
            ]
        ]);
    });

    /**
     * Organization Context
     */
    Route::resource('organizations', 'OrganizationController', [
        'except' => [
            'create', 'edit'
        ]
    ]);
    Route::group(['prefix' => 'organizations/{organization}', 'as' => 'organization.'], function () {
        require 'entity-routes.php';

        Route::resource('organization-managers', 'Organization\OrganizationManagerController', [
            'except' => [
                'create', 'edit', 'show',
            ]
        ]);
    });

    /**
     * Roles Context
     */
    Route::resource('roles', 'RoleController', [
        'only' => [
            'index'
        ]
    ]);

    /**
     * Statistics Context
     */
    Route::resource('statistics', 'StatisticController', [
        'only' => [
            'index', 'store', 'show', 'update', 'destroy',
        ]
    ]);
});
