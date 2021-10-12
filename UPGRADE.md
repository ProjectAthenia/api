# Athenia App Upgrade Guide

To upgrade from previous version of Athenia please check each version number listed below step by step. With every update make sure to run `php artisan ide-helper:models --smart-reset`

## 1.4.2

Simple one! Copy over `code/app/Repositories/BaseRepositoryAbstract.php` to allow for in and not in filters.

## 1.4.1

* code/tests/Integration/Repositories/User/UserRepositoryTest.php - Simple fix to function name

## 1.4.0

Nice little one! This adds a new command that allows for the resending of a message based on it's ID.

* code/app/Console/Commands/ResendMessageCommand.php
* code/tests/Unit/Console/Command/ResendMessageCommandTest.php

## 1.3.0

Another little one! This one adds a new testing trait to mock console output, and also fixes an issue with the ansible that came from the recent release of php8.

* ansible/roles/php/tasks/main.yml - updated imagick package name
* code/tests/Integration/Console/Commands/ReindexResourcesTest.php - Updated to use the new trait to ignore console ouput
* code/tests/Traits/MocksConsoleOutput.php - New trait for ignoring console output with commands

## 1.2.0

This little update adds a helper function to the message repository that makes it simple to send a user to all super admins in the system.

* code/app/Contracts/Repositories/User/MessageRepositoryContract.php - Added new function `sendEmailToSuperAdmins`
* code/app/Contracts/Repositories/User/UserRepositoryContract.php - Added new function `findSuperAdmins`
* code/app/Providers/AtheniaRepositoryProvider.php - Updated MessageRepository to pass in UserRepository as a parameter, and updated UserRepository to pass the application config as a parameter
* code/app/Repositories/User/MessageRepository.php - Added new function `sendEmailToSuperAdmins` and injected the user repository
* code/app/Repositories/User/UserRepository.php - Added new function `findSuperAdmins` and injected the application config
* code/database/migrations/2021_04_19_142743_make_password_nullable.php - New Migration
* code/tests/Integration/Console/Commands/ReindexResourcesTest.php - Updated User repository constructor
* code/tests/Integration/Http/Middleware/SearchFilteringMiddlewareTest.php - Updated for laravel 8 update, and fixed route
* code/tests/Integration/Repositories/User/MessageRepositoryTest.php - Added new test for new function
* code/tests/Integration/Repositories/User/UserRepositoryTest.php - Added new test for new function

## 1.1.0

This is a minor little one that fixes a bug in the search middleware for postgres, and adds firebase configuration to the services.

* code/app/Http/Middleware/SearchFilterParsingMiddleware.php - Reworked for passing through ilike instead of like is postgres is in use
* code/config/services.php - Added fcm settings to the config
* code/tests/Integration/Http/Middleware/SearchFilteringMiddlewareTest.php - Added test for case insensitive search

## 1.0.0

Welcome to 1.0.0! This version officially marks the first API stable version of Athenia. It is not nearly as exciting as it sounds, and entirely exists due to the most recent laravel update. Laravel 8.0 brought a much more logical and integrated way of handling testing factories, which by its nature has created an update so substantial that we now have a 1.0! Before you begin this update, consult the UPGRADE-0.x doc to make sure you are at least up to date with 0.54.0. Once that is complete then you can start by following the steps below.

### Dependency Changes

* Remove "barryvdh/laravel-cors": "^1.0" and add "fruitcake/laravel-cors": "^2.0"
* cartalyst/stripe-laravel - goes from ^12.0 to ^13.0
* laravel/framework - goes from ^7.0 to ^8.0
* Add "guzzlehttp/guzzle": "^7.0.1"
* barryvdh/laravel-ide-helper - goes from ^2.6 to ^2.8
* facade/ignition - goes from ^2.0 to ^2.3.6
* laracasts/generators - goes from ^1.1 to ^2.0
* nunomaduro/collision - goes from ^4.1 to ^5.0

Then make sure to remove the `classmap` block in the autoload section, and add the following to the psr-4 section

* "Database\\Factories\\": "database/factories/"
* "Database\\Seeders\\": "database/seeders/"

### Base Model Changes

In the `BaseModelAbstract` the trait `HasFactory` needs to be added, and then the `$guarded` should be emptied out to maintain compatibility.

### Failed Jobs Changes

A uuid was added to the failed jobs to work with a new laravel batch feature. With this a new migration has been created at `code/database/migrations/2021_01_04_220915_create_failed_jobs_table.php`. If the failed jobs table already exists in yoru app then the create portion of the migration should be swapped with a migration that simply adds the uuid field. Then `'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),` should be added to the failed array within the `queue` config.

### Seeders

The seeds directory needs to be renamed to seeders, and the new namespace needs to be added to the existing php files.

### Factories

This is the largest change, which also caused the bump to 1.0.0. Every single use of a factory has been changed, and every child app should be updated accordingly. To complete this update follow each step very carefully.

#### Factory Namespace

This is the easiest step, and you can start by copying over everything in the `app/database/factories` directory. Some files will be overridden, and those files should be checked with a diff to see if there were more than one factory within them before the update, which was not apart of Athenia. Then, every single non-Athenia factory should have a new factory created for it that follows the model directory structure of the primary app.

#### Utility Testing Updates

Once the app factories are updated there will be a couple of utility files that need to be updated related to testing before we start the larger updates. These files are as follows.

* code/phpunit.xml - General cleanup
* code/tests/TestCase.php - The act as user factory call has been updated
* code/tests/Traits/RolesTesting.php The getUserOfRole function factory call has been updated

#### Feature & Integration Tests

This is the most time intensive upgrade, and there are two recommended ways to manage this. Every single factory call has been updated for the new format, so every single integration and feature test will need to be updated. 

A simple way to start is to run a find and replace regex in phpunit with the search field set to `factory\((.*)?class\)\s*(>*)` and the replace field set to `$1factory()`. Once that is done, you will need to search for ` factory` to find any remaining pieces that use a count variable with more advanced composites of the factory function. The next replace should start by running another find and replace with `factory\((.*)?class,(.*?)\)\s*(>*)` as the regex and `$1factory()->count($2)` as the replace value. Once that is done, tests will need to be ran due to how large of a change this is.


