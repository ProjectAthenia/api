# Athenia App Upgrade Guide

To upgrade from previous version of Athenia please check each version number listed below step by step. With every update make sure to run `php artisan ide-helper:models --smart-reset`

The fastest way to upgrade is to run the following commands from your repos root

* cp  $ATHENIA_REPO/docker-compose.yml ./
* cp  $ATHENIA_REPO/dev_login.sh ./
* cp  $ATHENIA_REPO/.env.example ./
* rsync -arv $ATHENIA_REPO/dockerfiles ./
* rsync -arv $ATHENIA_REPO/extras ./
* rsync -arv $ATHENIA_REPO/code ./ --exclude vendor  --exclude storage  --exclude '.env' --exclude code/app/Providers

After that, you always want to make sure you inspect all changes, and you still want to go through the change log to check for moved files and deleted files, as rsync cannot check for deleted files, since it would delete any files created for the child application.

# 3.5.0

This version rearranges all requests to make sure they are easy to modify going forward. After rsync is ran, make sure to inspect all request changes to identify any customizations previously made. All of those customizations should now be put into the new Requests structure in app/Http/Core/Requests, as opposed to app/Athenia/Http/Core/Requests. The request code/app/Athenia/Http/Core/Requests/MembershipPlan/RetrieveRequest.php and code/app/Athenia/Http/Core/Requests/Organization/RetrieveRequest.php should both be deleted after rsync as those have been renamed to `ViewRequest`.

This version also updates the file `code/app/Athenia/Mail/MessageMailer.php` to take in from information out of config.

This version also fixes a deprecation warning from a recent PHPUnit update. This again will be resolved mostly by syncing the code directory.

# 3.4.0

This update adds some miscellaneous changes as well as updating the environment to php 8.4. It also updates the dependencies to use laravel 11, which has resulted in the generators package to be removed from the base service provider. After updating the Athenia files above, make sure to update the following files.

* code/app/Models/User/User.php - Removed Swagger docs
* code/composer.json - Updates dependencies, make sure to inspect 

# 3.3.0

Nice little one again! For this one you only need to copy over `dev_login.sh` and `docker-compose.yml`. This update will allow you to now run all background jobs for the app by booting the docker compose with `docker compose --profile background up`. Running the docker compose normally will keep those pieces turned off.

# 3.2.0

This update includes a number of changes. It opens up the features endpoints to be available to all users, adds an endpoint for messages, adds some meta information to assets, and finally adds some new helpers for dealing with various files. 

To start, copy over the Athena app and tests module. The files changed in those modules are as follows...

* code/app/Athenia/Console/Commands/ReindexResources.php
* code/app/Athenia/Contracts/Services/ArchiveHelperServiceContract.php
* code/app/Athenia/Contracts/Services/Asset/AssetConfigurationServiceContract.php
* code/app/Athenia/Contracts/Services/Asset/AssetImportServiceContract.php - Renamed from code/app/Athenia/Contracts/Services/AssetImportServiceContract.php
* code/app/Athenia/Contracts/Services/Indexing/ResourceRepositoryServiceContract.php
* code/app/Athenia/Exceptions/Handler.php
* code/app/Athenia/Http/Core/Controllers/MessageControllerAbstract.php
* code/app/Athenia/Http/Core/Requests/Feature/IndexRequest.php
* code/app/Athenia/Http/Core/Requests/Feature/ViewRequest.php
* code/app/Athenia/Http/Core/Requests/Message/StoreRequest.php
* code/app/Athenia/Jobs/CalculateAssetDimensionsJob.php
* code/app/Athenia/Providers/BaseRepositoryProvider.php
* code/app/Athenia/Providers/BaseServiceProvider.php
* code/app/Athenia/Repositories/AssetRepository.php
* code/app/Athenia/Repositories/BaseRepositoryAbstract.php
* code/app/Athenia/Repositories/User/ProfileImageRepository.php
* code/app/Athenia/Services/ArchiveHelperService.php
* code/app/Athenia/Services/Asset/AssetConfigurationService.php
* code/app/Athenia/Services/AssetImportService.php - Renamed from code/app/Athenia/Services/Asset/AssetImportService.php
* code/app/Athenia/Services/Indexing/BaseResourceRepositoryService.php

* code/tests/Athenia/Feature/Http/Feature/FeatureIndexTest.php
* code/tests/Athenia/Feature/Http/Feature/FeatureViewTest.php
* code/tests/Athenia/Feature/Http/Message/MessageCreateTest.php
* code/tests/Athenia/Feature/Http/Organization/Collection/OrganizationCollectionCreateTest.php
* code/tests/Athenia/Feature/Http/User/Thread/Message/UserThreadMessageCreateTest.php
* code/tests/Athenia/Integration/Console/Commands/ReindexResourcesTest.php
* code/tests/Athenia/Integration/Policies/Collection/CollectionItemPolicyTest.php
* code/tests/Athenia/Integration/Policies/Collection/CollectionPolicyTest.php
* code/tests/Athenia/Integration/Policies/FeaturePolicyTest.php
* code/tests/Athenia/Integration/Repositories/AssetRepositoryTest.php
* code/tests/Athenia/Integration/Repositories/Collection/CollectionRepositoryTest.php
* code/tests/Athenia/Integration/Repositories/User/ProfileImageRepositoryTest.php
* code/tests/Athenia/Unit/Console/Commands/AuditAssetDimensionsCommandTest.php
* code/tests/Athenia/Unit/Exceptions/HandlerTest.php
* code/tests/Athenia/Unit/Models/CategoryTest.php
* code/tests/Athenia/Unit/Services/ArchiveHelperServiceTest.php
* code/tests/Athenia/Unit/Services/Asset/AssetConfigurationServiceTest.php
* code/tests/Athenia/Unit/Services/Asset/AssetImportServiceTest.php
* code/tests/Athenia/Unit/Services/Indexing/BaseResourceRepositoryServiceTest.php

Then update the following files that live outside of the main Athenia modules

* code/app/Http/V1/Controllers/MessageController.php - New Controller
* code/app/Models/Category.php - Removed default order
* code/app/Models/Messaging/Message.php - Updated validation rules for general endpoint
* code/app/Policies/FeaturePolicy.php - Relaxed Policy
* code/app/Services/.gitkeep - Added to make sure directory is kept
* code/app/Services/Indexing/ResourceRepositoryService.php - Complete reworked
* code/composer.json - Added zip extension
* code/config/services.php - Added Slack config
* code/database/migrations/2024_06_20_005446_add_meta_to_assets.php - New migration for meta data
* code/routes/core.php - Moved features endpoint and added messages endpoint
* dev_login.sh - Made identification more accurate

# 3.1.0

Little breather after the big boy this one re-adds the scaffolding needed to run the app via a web layer. The only files within the app that needs to be updated is `code/app/Http/Kernel.php`, and `code/bootstrap/app.php`. The `code/app/Athenia` module should also be copied over.

# 3.0.0

This is a massive update. The vast majority of the code for both the main app and the tests have been moved into new namespaces in preparation to move all the templated code into its own package. Along with that, vagrant has been replaced with docker along with a new set of github actions. The entire messaging apparatus has also been reworked.

## Environment Changes

To start remove the following paths

* Vagrantfile
* ansible
* upgrade.json
* vagrant-do-provision.sh

Then copy over the following new paths

* .env.example
* .github/
* .gitignore
* dev_login.sh
* docker-compose.yml
* dockerfiles/

You then need to finish the following tasks.

## Athenia Restructure

There are new testing and app namespaces setup. The easiest way to make this change is to remove all of the listed files, and copy over `code/app/Athenia`, and `code/tests/Athenia`. Take special care to check any customizations made to any files in these directories to make sure that you leave your customized files in place.

Removed paths. Make sure to check any remaining files outside the scope of Athenia when directories were removed. Also, make sure that none of these files have customizations.

* code/app/Console/Commands/ReindexResources.php
* code/app/Console/Commands/ResendMessageCommand.php
* code/app/Contracts/ - Services & Repositories remain empty
* code/app/Events/
* code/app/Exceptions/
* code/app/Http/Core/ - Controllers & Requests remain empty
* code/app/Http/Middleware/
* code/app/Jobs/CanDisplayOutputAbstractJob.php - Only file that was in the directory from Athenia
* code/app/Listeners/Article/ArticleVersionCreatedListener.php - Only file that was in the directory from Athenia
* code/app/Listeners/Message/ - This directory was moved to Athenia, and it has also been renamed to Messaging
* code/app/Listeners/Payment/DefaultPaymentMethodSetListener.php - Only file that was in the directory from Athenia
* code/app/Listeners/User/ForgotPasswordListener.php - Only file removed from the root directory
* code/app/Listeners/User/UserMerge/
* code/app/Mail/
* code/app/Models/BaseModelAbstract.php
* code/app/Models/Traits/
* code/app/Observers/
* code/app/Policies/BaseBelongsToOrganizationPolicyAbstract.php
* code/app/Policies/BasePolicyAbstract.php
* code/app/Providers/AtheniaRepositoryProvider.php
* code/app/Repositories/
* code/app/Services/
* code/app/ThreadSecurity/
* code/app/Traits/CanGetAndUnset.php - Only file that was in the directory from Athenia
* code/app/Validators/

After that, the following paths have changes. Anything that has not been specifically mentioned within app should still be looked at to see if imports have been changed.

* code/phpunit.xml - Added new test suites for Athenia stuff that runs first.
* code/app/Console/Kernel.php - This file has been completely reworked.
* code/app/Listeners/User/Contact/ContactCreatedListener.php - Subject changed
* code/app/Policies/ - Make sure to take special note while inspecting these files as the contact `IsAnEntity` was moved and then renamed to `IsAnEntityContract`
* code/bootstrap/app.php - The namespace for the Exception Handler was updated

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
* code/app/Models/Organization/Organization.php - Large rework, best to review
* code/app/Models/User/User.php
* code/config/athenia.php
* code/config/services.php
* code/database/factories/Messaging/ - New
* code/database/factories/User/MessageFactory.php - Removed
* code/database/factories/User/ThreadFactory.php - Removed
* code/database/migrations/2024_04_16_142402_expand_messages_relations.php
* code/tests/Unit/Listeners/Message/MessageCreatedListenerTest.php
* extras/

## Providers Restructure

* code/app/Providers/AppRepositoryProvider.php
* code/app/Providers/AppServiceProvider.php
* code/app/Providers/AppValidatorProvider.php
* code/app/Providers/AuthServiceProvider.php
* code/app/Providers/EventServiceProvider.php
* code/app/Providers/RouteServiceProvider.php

## Laravel Update

Along with this update, Laravel has been updated to 10.x. So much has been updated in the whole code base, that documenting the changes for composer is a bit difficult. To complete this update, check the changes to the composer.json within Athenia, then check any dependencies specific to your project, and finally inspect any custom code for your app to see if there are any differences. 

After that, the validation strings at `code/resources/lang/en/validation.php` has been moved to `code/lang/en/`

## Last Notes

Now that all of this is done, it is best to check the .env.example to see any changes, and update your env for those changes.
