# Athenia App Upgrade Guide

To upgrade from previous version of Athenia please check each version number listed below step by step. With every update make sure to run `php artisan ide-helper:models --smart-reset`

# 2.7.0

## Laravel 9 Upgrade

This turned out to be a really easy upgrade. First off, inspect your composer.json for differences with the main project, and check anything not listed. The following packages have been completly removed from the core.

* fruitcake/laravel-cors
* facade/ignition
* phploc/phploc

### CORS change

Laravel now has built in CORS middlewares. In code/app/Http/Kernel.php you need to change the import `Fruitcake\Cors\HandleCors;` to `Illuminate\Http\Middleware\HandleCors;`.

### Testing changes

All testing files have been updated with type signatures. This can be handled easily by running phpunit shift https://laravelshift.com/shifts. The phpunit version should remain the same though. In addition, update the following files.

* code/tests/Traits/MocksConsoleOutput.php - Changed references to Progress mock due to changes in Laravel
* code/tests/bootstrap.php - Added a new package that allows mocking final classes

### General changes

The file `code/app/Jobs/CanDisplayOutputAbstractJob.php` now has type signatures to prepare for the eventual php 9 upgrade. The lang directory in `code/resources`, should be moved to `code`

# 2.6.0

## New Module

This update adds a new collection module! The core has also been updated to use php8.3.

### General Changes

* code/tests/Feature/Http/Organization/Asset/OrganizationAssetCreateTest.php - Namespace fixed
* code/tests/Traits/ReflectionHelpers.php - Fixed directory bug

### php8.3

* ansible/roles/app/templates/api.projectathenia.com.conf.j2 - Updated php fpm reference
* ansible/roles/mysql/tasks/main.yml - Removed root password
* ansible/roles/php/tasks/main.yml - Updated php install packages
* code/composer.lock - Updated dependencies for php 8.3

### Collection Module

* code/app/Contracts/Models/IsAnEntity.php - Added Collection relation
* code/app/Contracts/Repositories/Collection/ - New Path
* code/app/Contracts/Services/Collection/ - New Path
* code/app/Http/Core/Controllers/Collection/ - New Path
* code/app/Http/Core/Controllers/CollectionControllerAbstract.php - New Path
* code/app/Http/Core/Controllers/CollectionItemControllerAbstract.php - New Path
* code/app/Http/Core/Controllers/Entity/CollectionControllerAbstract.php - New Path
* code/app/Http/Core/Requests/Collection/ - New Path
* code/app/Http/Core/Requests/CollectionItem/ - New Path
* code/app/Http/Core/Requests/Entity/Collection/ - New Path
* code/app/Http/V1/Controllers/Collection/ - New Path
* code/app/Http/V1/Controllers/CollectionController.php - New Path
* code/app/Http/V1/Controllers/CollectionItemController.php - New Path
* code/app/Http/V1/Controllers/Entity/CollectionController.php - New Path
* code/app/Models/Collection/ - New Path
* code/app/Models/Traits/IsEntity.php - Added Collection relation
* code/app/Policies/Collection/ - New Path
* code/app/Providers/AppServiceProvider.php - Registered new service
* code/app/Providers/AtheniaRepositoryProvider.php - Registered collection repos
* code/app/Providers/RouteServiceProvider.php - Registered collection route params
* code/app/Repositories/Collection/ - New Path
* code/app/Services/Collection/ - New Path
* code/database/factories/Collection/ - New Path
* code/database/migrations/2024_02_01_150536_add_user_collections_module.php - New Path
* code/routes/core.php - Registered new routes
* code/routes/entity-routes.php - Registered new routes
* code/tests/Feature/Http/Collection/ - New Path
* code/tests/Feature/Http/CollectionItem/ - New Path
* code/tests/Feature/Http/Organization/Collection/ - New Path
* code/tests/Feature/Http/User/Collection/ - New Path
* code/tests/Integration/Policies/Collection/ - New Path
* code/tests/Integration/Repositories/Collection/ - New Path
* code/tests/Unit/Models/Collection/ - New Path
* code/tests/Unit/Services/Collection/ - New Path

# 2.5.0

## New validator!

* code/app/Providers/AppValidatorProvider.php - Registered the new validator
* code/app/Validators/OwnedByValidator.php - The new validator
* code/resources/lang/en/validation.php - Registered the validator message
* code/tests/Unit/Validators/OwnedByValidatorTest.php - The test for the validator

## Minor clean up.

* code/routes/entity-routes.php - Added instructional comment block
* code/tests/Feature/Http/Organization/Payment/OrganizationPaymentIndexTest.php - Fixed Namespace
* code/tests/Feature/Http/User/Payment/UserPaymentIndexTest.php - Fixed Namespace

# 2.4.0

This update adds a new category model and set of endpoints. To complete this update, copy over the following new paths.

* code/app/Contracts/Repositories/CategoryRepositoryContract.php 
* code/app/Http/Core/Controllers/CategoryControllerAbstract.php
* code/app/Http/Core/Requests/Category/ 
* code/app/Http/V1/Controllers/CategoryController.php 
* code/app/Models/Category.php 
* code/app/Policies/CategoryPolicy.php 
* code/app/Repositories/CategoryRepository.php 
* code/database/factories/CategoryFactory.php 
* code/database/migrations/2023_10_14_184520_add_categories_table.php 
* code/tests/Feature/Http/Category/
* code/tests/Integration/Policies/CategoryPolicyTest.php 
* code/tests/Integration/Repositories/CategoryRepositoryTest.php 
* code/tests/Unit/Models/CategoryTest.php

Then the following existing files need to be updated

* code/app/Providers/AtheniaRepositoryProvider.php - New repo registered
* code/app/Providers/RouteServiceProvider.php - New route mapping registered
* code/routes/core.php - New Route registered

# 2.3.0

This updates adds a new service that will help with copying directories between file systems. To complete this update copy over the following files.

* code/app/Console/Kernel.php - Unrelated, added default schedule function, probably dont need if you are using the command kernel
* code/app/Contracts/Services/DirectoryCopyServiceContract.php - New Service Contract
* code/app/Services/DirectoryCopyService.php - New Service
* code/tests/Unit/Services/DirectoryCopyServiceTest.php - New Test
* code/app/Providers/AppServiceProvider.php - Registered new service

# 2.2.0

This is another simple one. This adds a large amount of functionality to the search api. To run this update simply copy over `code/app/Repositories/BaseRepositoryAbstract.php`.

## 2.1.0

This is a simple one. This adds a new abstract job that is designed to be used both by commands, and any other entry points in the app to allow for this sort of functionality to be more easily reused regardless of whether or not someone is sitting at a console. To complete this update copy over the following new files.

* code/app/Jobs/CanDisplayOutputAbstractJob.php
* code/tests/Unit/Jobs/CanDisplayOutputAbstractJobTest.php

## 2.0.0

Another big one! This one brings the app up to PHP 8, which involves quite a few pieces of the app changing. Each section that needs to be updated is separated below.

### Dependencies

The following packages have been completely removed...

* nochso/diff
* orchid/socket
* ralouphie/mimey
* tymon/jwt-auth
* fzaninotto/faker - dev

The follonw packages have been added...

* php-open-source-saver/jwt-auth
* pusher/pusher-php-server
* sebastian/diff
* xantios/mimey
* fakerphp/faker - dev

Then the package phploc/phploc needs to be upgraded from ^6.0 to ^7.0

The config `code/config/app.php` also needs to be updated for the change in JWT auth

### Environment changes

The application level ansible stuff was renamed from `athenia` to `app`, php has been updated to 8.2, the environment has been updated to ubuntu 22, and postgress has been changed to favor mysql. All changes after that are listed below.

* Vagrantfile - Changed box settings, and updated for Parallels and Arm
* ansible/playbook.yml - Updated athenia reference to app
* ansible/roles/app/templates/api.projectathenia.com.conf.j2 - Changed PHP version to 8.2
* ansible/roles/common/tasks/main.yml - Added pip install and removed unattended upgrades
* ansible/roles/mysql/ - Added module
* ansible/roles/php/tasks/main.yml - Changed all package installs to related 8.2 versions
* ansible/playbook.yml - Changed postgres to mysql and changed athenia to app
* ansible/roles/postgres/tasks/main.yml - Updated python bindings install
* code/.env.example - Changed default db driver to mysql
* vagrant-do-provision.sh - Updated script to run on ubuntu 22

### Socket Changes

The socket article functionality has entirely been reworked. With this, a number of cleanup tasks have also been completed including renaming the Iteration model to ArticleIteration.

* code/app/Contracts/Repositories/Wiki/{IterationRepositoryContract.php => ArticleIterationRepositoryContract.php}
* code/app/Contracts/Repositories/Wiki/ArticleModificationRepositoryContract.php
* code/app/Contracts/Services/Wiki/ArticleModificationApplicationServiceContract.php
* code/app/Contracts/Services/{ => Wiki}/ArticleVersionCalculationServiceContract.php
* code/app/Http/Core/Controllers/Article/IterationControllerAbstract.php
* code/app/Http/Core/Requests/Article/Iteration/IndexRequest.php - Updated policy name
* code/app/Http/Sockets/ArticleIterations.php - Removed
* code/app/Listeners/Article/ArticleVersionCreatedListener.php - References to Iteration changed to ArticleIteration
* code/app/Listeners/User/UserMerge/UserCreatedIterationsMergeListener.php - References to Iteration changed to ArticleIteration
* code/app/Models/User/User.php - References to Iteration changed to ArticleIteration
* code/app/Models/Wiki/Article.php - Massive changes for the iteration name change
* code/app/Models/Wiki/{Iteration.php => ArticleIteration.php}
* code/app/Models/Wiki/ArticleModification.php
* code/app/Models/Wiki/ArticleVersion.php
* code/app/Policies/Wiki/{IterationPolicy.php => ArticleIterationPolicy.php}
* code/app/Providers/AppServiceProvider.php - Namespace for ArticleVersionCalculationServiceContract was renamed
* code/app/Providers/AtheniaRepositoryProvider.php
* code/app/Providers/RouteServiceProvider.php - Updated article iteration mapping
* code/app/Repositories/Wiki/{IterationRepository.php => ArticleIterationRepository.php}
* code/app/Repositories/Wiki/ArticleModificationRepository.php
* code/app/Services/Wiki/ArticleModificationApplicationService.php
* code/app/Services/{ => Wiki}/ArticleVersionCalculationService.php
* code/app/Validators/ArticleVersion/SelectedIterationBelongsToArticleValidator.php
* code/config/websockets.php
* code/database/factories/Vote/BallotItemFactory.php
* code/database/factories/Vote/BallotItemOptionFactory.php
* code/database/factories/Wiki/{IterationFactory.php => ArticleIterationFactory.php}
* code/database/factories/Wiki/ArticleModificationFactory.php
* code/database/factories/Wiki/ArticleVersionFactory.php
* code/database/migrations/0000_00_00_000000_create_websockets_statistics_entries_table.php
* code/database/migrations/2019_10_29_154335_cusco.php
* code/database/migrations/2021_08_08_161807_create_article_modifications_table.php
* code/resources/lang/en/validation.php
* code/routes/core.php
* code/tests/Feature/Http/Article/ArticleVersion/ArticleVersionCreateTest.php
* code/tests/Feature/Http/Article/ArticleViewTest.php
* code/tests/Feature/Http/Article/Iteration/ArticleIterationIndexTest.php
* code/tests/Feature/Socket/ArticleIterationTest.php
* code/tests/Integration/Http/Sockets/ArticleIterationTest.php
* code/tests/Integration/Models/Wiki/ArticleTest.php
* code/tests/Integration/Policies/Wiki/IterationPolicyTest.php
* code/tests/Integration/Repositories/Vote/BallotItemRepositoryTest.php
* code/tests/Integration/Repositories/Vote/BallotRepositoryTest.php
* code/tests/Integration/Repositories/Wiki/{IterationRepositoryTest.php => ArticleIterationRepositoryTest.php}
* code/tests/Integration/Repositories/Wiki/ArticleModificationRepositoryTest.php
* code/tests/Integration/Repositories/Wiki/ArticleVersionRepositoryTest.php
* code/tests/Unit/Http/Sockets/ArticleIterationsTest.php
* code/tests/Unit/Listeners/Article/ArticleVersionCreatedListenerTest.php
* code/tests/Unit/Listeners/User/UserMerge/UserCreatedIterationsMergeListenerTest.php
* code/tests/Unit/Models/User/UserTest.php
* code/tests/Unit/Models/Wiki/{IterationTest.php => ArticleIterationTest.php}
* code/tests/Unit/Models/Wiki/ArticleModificationTest.php
* code/tests/Unit/Models/Wiki/ArticleTest.php
* code/tests/Unit/Models/Wiki/ArticleVersionTest.php
* code/tests/Unit/Services/Wiki/ArticleModificationApplicationServiceTest.php
* code/tests/Unit/Services/{ => Wiki}/ArticleVersionCalculationServiceTest.php
* code/tests/Unit/Validators/ArticleVersion/SelectedIterationBelongsToArticleValidatorTest.php

### JWT Package Change

The old JWT package is no longer being maintained, so that has been replaced. By simply running a find and replace for Tymon -> PHPOpenSourceSaver most of the file changes should be addressed. The below files will automatically be updated when that happens.

* code/app/Exceptions/Handler.php
* code/app/Http/Core/Controllers/AuthenticationControllerAbstract.php
* code/app/Http/Middleware/JWTGetUserFromTokenProtectedRouteMiddleware.php
* code/app/Http/Middleware/JWTGetUserFromTokenUnprotectedRouteMiddleware.php
* code/config/jwt.php
* code/tests/Feature/Http/Authentication/LogoutTest.php
* code/tests/Unit/Http/Middleware/JWTGetUserFromTokenProtectedRouteMiddlewareTest.php
* code/tests/Unit/Http/Middleware/JWTGetUserFromTokenUnprotectedRouteMiddlewareTest.php

### Default message order

The default order for the message endpoint has been updated to be explicit if an order is not passed in. To complete this update update this `code/app/Http/Core/Controllers/User/Thread/MessageControllerAbstract.php` controller and this ` code/app/Repositories/User/MessageRepository.php` repository file.

### Miscellaneous

* code/config/broadcasting.php - Updated for recent version of laravel
* code/app/Providers/BroadcastServiceProvider.php - Created
* code/routes/channels.php - Created
* code/tests/Unit/Http/Core/Requests/BaseAssetUploadRequestAbstractTest.php - mime type change for SVG
