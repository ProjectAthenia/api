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