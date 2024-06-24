# Athenia App Upgrade Guide

To upgrade from previous version of Athenia please check each version number listed below step by step. With every update make sure to run `php artisan ide-helper:models --smart-reset`

The fastest way to upgrade is to run the following commands from your repos root

* cp  $ATHENIA_REPO/docker-compose.yml ./
* cp  $ATHENIA_REPO/.env.example ./
* rsync -arv $ATHENIA_REPO/dockerfiles ./
* rsync -arv $ATHENIA_REPO/extras ./
* rsync -arv $ATHENIA_REPO/code ./ --exclude vendor  --exclude storage  --exclude '.env'

After that, you always want to make sure you inspect all changes, and you still want to go through the change log to check for moved files and deleted files, as rsync cannot check for deleted files, since it would delete any files created for the child application.

# 3.1.0

Little breather after the big boy this one re-adds the scaffolding needed to run the app via a web layer. The only file within the app that needs to be updated is `code/app/Http/Kernel.php`. The `code/app/Athenia` module should also be copied over.

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
