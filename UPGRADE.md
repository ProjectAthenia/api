# Athenia App Upgrade Guide

To upgrade from previous version of Athenia please check each version number listed below step by step. With every update make sure to run `php artisan ide-helper:models --smart-reset`

The fastest way to upgrade is to run the following commands from your repos root

* cp  $ATHENIA_REPO/docker-compose.yml ./
* cp  $ATHENIA_REPO/.env.example ./
* rsync -arv $ATHENIA_REPO/dockerfiles ./
* rsync -arv $ATHENIA_REPO/extras ./
* rsync -arv $ATHENIA_REPO/code ./ --exclude vendor  --exclude storage  --exclude '.env'

After that, you always want to make sure you inspect all changes, and you still want to go through the change log to check for moved files and deleted files, as rsync cannot check for deleted files, since it would delete any files created for the child application.

# 3.0.0

This is a massive update. The vast majority of the code for both the main app and the tests have been moved into new namespaces in preparation to move all the templated code into its own package. Along with that, vagrant has been replaced with docker along with a new set of github actions. The entire messaging apparatus has also been reworked.

## Environment Changes

To start remove the following paths

* Vagrantfile
* ansible

Then copy over the following new paths

* .env.example
* .github/workflows/code-checks.yaml
* .gitignore

You then need to finish the following tasks.

## Athenia Restructure

There are new testing and app namespaces setup. The easiest way to make this change is to remove all of the listed files, and copy over `code/app/Athenia`, and `code/tests/Athenia`. Take special care to check any customizations made to any files in these directories to make sure that you leave your customized files in place.

Removed paths. Make sure to check any remaining files outside the scope of Athenia when directories were removed.

* code/app/Console/Commands/ReindexResources.php
* code/app/Console/Commands/ResendMessageCommand.php
* code/app/Contracts/ - Services & Repositories remain empty
* code/app/Events/
* code/app/Exceptions/
* code/app/Http/Core/Controllers/ - Controllers & Requests remain empty

After that, the following paths have changes.

* code/phpunit.xml - Added new test suites for Athenia stuff that runs first.
* code/app/Console/Commands/ChargeRenewal.php - Imports Updated
* code/app/Console/Commands/SendRenewalReminders.php - Imports Updated
* code/app/Console/Kernel.php - This file has been completely reworked.

Finally, the following directories now have a gitkeep to make sure the structure for the app stays in place

* code/app/Contracts/
* code/app/Contracts/Repositories/
* code/app/Contracts/Services/
* code/app/Events/
* code/app/Http/Core/Controllers/
* code/app/Http/Core/Requests/
* code/app/Observers/
* code/app/Repositories/
* code/app/Services/
* code/tests/Feature/
* code/tests/Integration/
* code/tests/Unit/

Once all of the changes have been applied, check all remaining paths in both tests and app to make sure any namespaces are updated.

## Messaging Restructure

* code/.env.example



* code/app/Contracts/Repositories/Collection/CollectionItemRepositoryContract.php
* code/app/Contracts/Repositories/Collection/CollectionRepositoryContract.php
* code/app/Contracts/Services/Collection/ItemInEntityCollectionServiceContract.php
* code/app/Http/Core/Requests/Category/IndexRequest.php
* code/app/Http/Core/Requests/Category/ViewRequest.php
* code/app/Http/Kernel.php
* code/app/Http/V1/Controllers/Article/ArticleVersionController.php
* code/app/Http/V1/Controllers/Article/IterationController.php
* code/app/Http/V1/Controllers/ArticleController.php
* code/app/Http/V1/Controllers/AuthenticationController.php
* code/app/Http/V1/Controllers/Ballot/BallotCompletionController.php
* code/app/Http/V1/Controllers/BallotController.php
* code/app/Http/V1/Controllers/CategoryController.php
* code/app/Http/V1/Controllers/Collection/CollectionItemController.php
* code/app/Http/V1/Controllers/CollectionController.php
* code/app/Http/V1/Controllers/CollectionItemController.php
* code/app/Http/V1/Controllers/Entity/AssetController.php
* code/app/Http/V1/Controllers/Entity/CollectionController.php
* code/app/Http/V1/Controllers/Entity/PaymentController.php
* code/app/Http/V1/Controllers/Entity/PaymentMethodController.php
* code/app/Http/V1/Controllers/Entity/ProfileImageController.php
* code/app/Http/V1/Controllers/Entity/SubscriptionController.php
* code/app/Http/V1/Controllers/FeatureController.php
* code/app/Http/V1/Controllers/ForgotPasswordController.php
* code/app/Http/V1/Controllers/MembershipPlan/MembershipPlanRateController.php
* code/app/Http/V1/Controllers/MembershipPlanController.php
* code/app/Http/V1/Controllers/Organization/OrganizationManagerController.php
* code/app/Http/V1/Controllers/OrganizationController.php
* code/app/Http/V1/Controllers/ResourceController.php
* code/app/Http/V1/Controllers/RoleController.php
* code/app/Http/V1/Controllers/StatusController.php
* code/app/Http/V1/Controllers/User/BallotCompletionController.php
* code/app/Http/V1/Controllers/User/ContactController.php
* code/app/Http/V1/Controllers/User/Thread/MessageController.php
* code/app/Http/V1/Controllers/User/ThreadController.php
* code/app/Http/V1/Controllers/UserController.php
* code/app/Listeners/Message/MessageCreatedListener.php
* code/app/Listeners/Organization/OrganizationManagerCreatedListener.php
* code/app/Listeners/User/Contact/ContactCreatedListener.php
* code/app/Listeners/User/SignUpListener.php
* code/app/Listeners/Vote/VoteCreatedListener.php
* code/app/Models/Asset.php
* code/app/Models/Category.php
* code/app/Models/Collection/Collection.php
* code/app/Models/Collection/CollectionItem.php
* code/app/Models/Feature.php
* code/app/Models/Messaging/Message.php
* code/app/Models/Messaging/PushNotificationKey.php
* code/app/Models/Messaging/Thread.php
* code/app/Models/Organization/Organization.php
* code/app/Models/Organization/OrganizationManager.php
* code/app/Models/Payment/LineItem.php
* code/app/Models/Payment/Payment.php
* code/app/Models/Payment/PaymentMethod.php
* code/app/Models/Resource.php
* code/app/Models/Role.php
* code/app/Models/Subscription/MembershipPlan.php
* code/app/Models/Subscription/MembershipPlanRate.php
* code/app/Models/Subscription/Subscription.php
* code/app/Models/User/Contact.php
* code/app/Models/User/PasswordToken.php
* code/app/Models/User/ProfileImage.php
* code/app/Models/User/User.php
* code/app/Models/Vote/Ballot.php
* code/app/Models/Vote/BallotCompletion.php
* code/app/Models/Vote/BallotItem.php
* code/app/Models/Vote/BallotItemOption.php
* code/app/Models/Vote/Vote.php
* code/app/Models/Wiki/Article.php
* code/app/Models/Wiki/ArticleIteration.php
* code/app/Models/Wiki/ArticleModification.php
* code/app/Models/Wiki/ArticleVersion.php
* code/app/Policies/AssetPolicy.php
* code/app/Policies/CategoryPolicy.php
* code/app/Policies/Collection/CollectionItemPolicy.php
* code/app/Policies/Collection/CollectionPolicy.php
* code/app/Policies/FeaturePolicy.php
* code/app/Policies/Messaging/MessagePolicy.php
* code/app/Policies/Messaging/ThreadPolicy.php
* code/app/Policies/Organization/OrganizationManagerPolicy.php
* code/app/Policies/Organization/OrganizationPolicy.php
* code/app/Policies/Payment/PaymentMethodPolicy.php
* code/app/Policies/Payment/PaymentPolicy.php
* code/app/Policies/ResourcePolicy.php
* code/app/Policies/RolePolicy.php
* code/app/Policies/Subscription/MembershipPlanPolicy.php
* code/app/Policies/Subscription/MembershipPlanRatePolicy.php
* code/app/Policies/Subscription/SubscriptionPolicy.php
* code/app/Policies/User/ContactPolicy.php
* code/app/Policies/User/ProfileImagePolicy.php
* code/app/Policies/User/UserPolicy.php
* code/app/Policies/Vote/BallotCompletionPolicy.php
* code/app/Policies/Vote/BallotPolicy.php
* code/app/Policies/Wiki/ArticleIterationPolicy.php
* code/app/Policies/Wiki/ArticlePolicy.php
* code/app/Policies/Wiki/ArticleVersionPolicy.php
* code/app/Providers/AppRepositoryProvider.php
* code/app/Providers/AppServiceProvider.php
* code/app/Providers/AppValidatorProvider.php
* code/app/Providers/AuthServiceProvider.php
* code/app/Providers/EventServiceProvider.php
* code/app/Providers/RouteServiceProvider.php
* code/bootstrap/app.php
* code/composer.json
* code/composer.lock
* code/config/athenia.php
* code/config/database.php
* code/config/services.php
* code/database/factories/Messaging/MessageFactory.php
* code/database/factories/Messaging/PushNotificationKeyFactory.php
* code/database/factories/Messaging/ThreadFactory.php
* code/database/migrations/2024_04_16_142402_expand_messages_relations.php
* code/lang/en/validation.php
* code/tests/Unit/Listeners/Message/MessageCreatedListenerTest.php
* dev_login.sh
* docker-compose.yml
* dockerfiles/nginx.dockerfile
* dockerfiles/nginx/default.conf
* dockerfiles/php.dockerfile
* dockerfiles/php/php.ini
* extras/SendPushNotificationServiceTest.php
* extras/SendSMSNotificationServiceTest.php
* extras/SendSlackNotificationServiceTest.php
* upgrade.json
* vagrant-do-provision.sh
* 