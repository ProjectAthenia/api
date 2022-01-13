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


* code/app/Contracts/Repositories/Wiki/{IterationRepositoryContract.php => ArticleIterationRepositoryContract.php}
* code/app/Contracts/Repositories/Wiki/ArticleModificationRepositoryContract.php
* code/app/Contracts/Services/Wiki/ArticleModificationApplicationServiceContract.php
* code/app/Contracts/Services/{ => Wiki}/ArticleVersionCalculationServiceContract.php
* code/app/Exceptions/Handler.php 
