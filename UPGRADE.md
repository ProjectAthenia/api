# Athenia App Upgrade Guide

To upgrade from previous version of Athenia please check each version number listed below step by step. With every update make sure to run `php artisan ide-helper:models --smart-reset`

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

### Ansible changes

The application level ansible stuff was renamed from `athenia` to `app`. All changes after that are listed below.

* ansible/playbook.yml - Updated athenia reference to app
* ansible/roles/app/templates/api.projectathenia.com.conf.j2 - Changed PHP version to 8.0
* ansible/roles/php/tasks/main.yml - Changed all package installs to related 8.0 versions

### Socket Changes

The socket article functionality has entirely been reworked. With this, a number of cleanup tasks have also ben completed.

* code/app/Contracts/Repositories/Wiki/{IterationRepositoryContract.php => ArticleIterationRepositoryContract.php}
* code/app/Contracts/Repositories/Wiki/ArticleModificationRepositoryContract.php
* code/app/Contracts/Services/Wiki/ArticleModificationApplicationServiceContract.php
* code/app/Contracts/Services/{ => Wiki}/ArticleVersionCalculationServiceContract.php
* code/app/Http/Core/Controllers/Article/IterationControllerAbstract.php


* code/app/Exceptions/Handler.php
* code/app/Http/Core/Controllers/AuthenticationControllerAbstract.php
* code/app/Http/Core/Controllers/User/Thread/MessageControllerAbstract.php
* code/app/Http/Core/Requests/Article/Iteration/IndexRequest.php
* code/app/Http/Middleware/JWTGetUserFromTokenProtectedRouteMiddleware.php
* code/app/Http/Middleware/JWTGetUserFromTokenUnprotectedRouteMiddleware.php
* code/app/Http/Sockets/ArticleIterations.php
* code/app/Listeners/Article/ArticleVersionCreatedListener.php
* code/app/Listeners/User/UserMerge/UserCreatedIterationsMergeListener.php
* code/app/Models/User/User.php
* code/app/Models/Wiki/Article.php
* code/app/Models/Wiki/{Iteration.php => ArticleIteration.php}
* code/app/Models/Wiki/ArticleModification.php
* code/app/Models/Wiki/ArticleVersion.php
* code/app/Policies/Wiki/{IterationPolicy.php => ArticleIterationPolicy.php}
* code/app/Providers/AppServiceProvider.php
* code/app/Providers/AtheniaRepositoryProvider.php
* code/app/Providers/BroadcastServiceProvider.php
* code/app/Providers/RouteServiceProvider.php
* code/app/Repositories/User/MessageRepository.php
* code/app/Repositories/Wiki/{IterationRepository.php => ArticleIterationRepository.php}
* code/app/Repositories/Wiki/ArticleModificationRepository.php
* code/app/Services/Wiki/ArticleModificationApplicationService.php 
* code/app/Services/{ => Wiki}/ArticleVersionCalculationService.php
* code/app/Validators/ArticleVersion/SelectedIterationBelongsToArticleValidator.php
* code/config/app.php
* code/config/broadcasting.php
* code/config/jwt.php
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
* code/routes/channels.php
* code/routes/core.php
* code/tests/Feature/Http/Article/ArticleVersion/ArticleVersionCreateTest.php
* code/tests/Feature/Http/Article/ArticleViewTest.php
* code/tests/Feature/Http/Article/Iteration/ArticleIterationIndexTest.php
* code/tests/Feature/Http/Authentication/LogoutTest.php
* code/tests/Feature/Socket/ArticleIterationTest.php
* code/tests/Integration/Http/Sockets/ArticleIterationTest.php
* code/tests/Integration/Models/Wiki/ArticleTest.php
* code/tests/Integration/Policies/Wiki/IterationPolicyTest.php
* code/tests/Integration/Repositories/Vote/BallotItemRepositoryTest.php
* code/tests/Integration/Repositories/Vote/BallotRepositoryTest.php
* code/tests/Integration/Repositories/Wiki/{IterationRepositoryTest.php => ArticleIterationRepositoryTest.php}
* code/tests/Integration/Repositories/Wiki/ArticleModificationRepositoryTest.php
* code/tests/Integration/Repositories/Wiki/ArticleVersionRepositoryTest.php
* code/tests/Unit/Http/Core/Requests/BaseAssetUploadRequestAbstractTest.php
* code/tests/Unit/Http/Middleware/JWTGetUserFromTokenProtectedRouteMiddlewareTest.php
* code/tests/Unit/Http/Middleware/JWTGetUserFromTokenUnprotectedRouteMiddlewareTest.php
* code/tests/Unit/Http/Sockets/ArticleIterationsTest.php
* code/tests/Unit/Listeners/Article/ArticleVersionCreatedListenerTest.php
* code/tests/Unit/Listeners/User/UserMerge/UserCreatedIterationsMergeListenerTest.php
* code/tests/Unit/Models/User/UserTest.php
* code/tests/Unit/Models/Wiki/ArticleIterationTest.php
* code/tests/Unit/Models/Wiki/ArticleModificationTest.php
* code/tests/Unit/Models/Wiki/ArticleTest.php
* code/tests/Unit/Models/Wiki/ArticleVersionTest.php
* code/tests/Unit/Models/Wiki/IterationTest.php
* code/tests/Unit/Services/Wiki/ArticleModificationApplicationServiceTest.php
* code/tests/Unit/Services/{ => Wiki}/ArticleVersionCalculationServiceTest.php
* code/tests/Unit/Validators/ArticleVersion/SelectedIterationBelongsToArticleValidatorTest.php 
