# Athenia App Read Me 

## Project Setup

In order to get everything ready you will need to make sure that you have docker installed in your system. Once you have that setup, you are going to want to setup your docker environment by running `cd .env.example .env` in the root of the project. After that run `docker compose build` in order to allow the development environment to build. You can now run `docker compose up` to bring the environment up.

### Accessing the Environment

A handy little script has been included at the root named `dev_login.sh`, which will log you into the PHP app container when ran. Within that container, you can interact with artisan and phpunit with full access to the docker PHP environment.

### Setting up the App

The final bit of setup has to do with setting up the remaining environment variables for the actual app. In order to do this run `cd .env.example .env` from within the code directory. In your env file, while running in docker, make sure your `DB_HOST` variable is set to `mysql`. The mysql test DB is configured as if it is always running in docker.

Within the api-php container you then want to run `php artisan key:generate && php artisan jwt:secret` from the root of the shared mount. Finally run `php artisan migrate` in order to get the database setup, and finish the setup. You can then run `./vendor/bin/phpunit` to verify the tests are running, and passing. Finally, on your host, you can attempt to access the web server from http://localhost:{EXPOSED_HTTP_PORT}/v1/status where you should see a simple JSON with status OK.

## Optional Message Delivery

By default, the template is designed to deliver messages via email. Capabilities to deliver via SMS, push, and Slack are all available, but they need to be enabled manually. To enable those channel, complete the following steps.

### SMS

SMS is delivered via Twilio. To get started with this channel, you must first install the dependency `laravel-notification-channels/twilio`. Once that is installed you then need to symlink the file `extras/SendSMSNotificationServiceTest.php` to `code/tests/Athenia/Unit/Services/Messaging/SendSMSNotificationServiceTest.php`. Finally, set the following config variables in your .env.

* SMS_NOTIFICATOINS_ENABLED - needs to now be set to true
* TWILIO_ACCOUNT_SID - Generated from twilio
* TWILIO_AUTH_TOKEN - Generated from twilio

### Push Notifications

Push Notifications are delivered via Firebase. To get started with this channel, install the dependency `davidvrsantos/laravel-fcm-notification`. Once that is installed you then need to symlink the file `extras/SendPushNotificationServiceTest.php` to `code/tests/Athenia/Unit/Services/Messaging/SendPushNotificationServiceTest.php`. Finally, set the following config variables in your .env.

* FIREBASE_NOTIFICATIONS_ENABLED - needs to now be set to true
* FCM_KEY - Generated from Firebase

### Slack

To get started with this channel, install the dependency `jolicode/slack-php-api`. Once that is installed you then need to symlink the file `extras/SendSlackNotificationServiceTest.php` to `code/tests/Athenia/Unit/Services/Messaging/SendSlackNotificationServiceTest.php`. Finally, set the following config variables in your .env.

* SLACK_NOTIFICATIONS_ENABLED = needs to now be set to true

## Development

This app is a slightly customized laravel app. Most of the docs for Laravel will explain how to do everything you may want to do within this app.

### Defining Routes

When you want to create a new set of routes there are a number of steps that you should do. Inside of the HTTP namespace you will find that there are two very important directories `Core` and `V1`. Each of these directories contain a set of controllers. The controllers in `Core` are all abstract, and are not meant to be implemented directly. The controllers in `V1` are the controllers that should be implemented for the API, and the ones in this project are simple extensions of the ones in the `Core` directory. 

The best practice is to have all of the controllers in the `Core` directory to have the majority of the required implementation with the most up to date manner of which you want to achieve your implementation. This means that your highest API version number should in most cases simply extend the abstract controllers in the core. The purpose of doing this is to make the introduction of backwards incompatible changes incredibly simple to do. 

When introducing a backwards incompatible change the first step would do would be to copy your current routes into a new namespace, copy over a new implementation of the current routes file, and then define the new group within the RouteServiceProvider. Once this is complete you can then take the old implementation, which should still be within the `Core` namespace and put the deprecated functionality into any old route groups that existed before the change. 

Following these steps should at all times keep the most recent version of routes as simple as possible while progressively adding to the complexity of older route groups for the purpose of legacy support.
