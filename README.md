# Athenia App Read Me 

## Project Setup

In order to get everything ready you will need to make sure that you have vagrant, virtualbox, ansible and the vagrant hosts plugin installed in your system. Once you have that setup run `vagrant up dev` in order to allow the development environment to build.

Once the vagrant has been installed the project dependencies will need to be installed. In order to do this login to the vm with `vagrant ssh dev`, and then run `sudo su` to become the super user. Once you are the super user run `cd /vagrant/code` in order to navigate to the projects root directory. Then run `composer install` in order to install all project dependencies.

The final bit of setup has to do with setting up the remaining environment variables. In order to do this run `cd .env.example .env` from within the project root in the vagrant. Then run `php artisan key:generate && php artisan jwt:secret` in order to generate the application hashes needed. Finally run `php artisan migrate` in order to get the database setup, and finish the setup.

## Accessing the Environment

Use the command `docker ps` to identify the id of the 

## Swagger

This project uses swagger to generate api documentation. The swagger file is located in docs/swagger.json - and should be double checked whenever changes are made to inline swagger docs.

To generate a fresh copy of swagger.json, run the following command from the root of the project within the VM:

`code/vendor/bin/swagger code/app --output docs/`

Model definitions should all be located at the bottom of a PHP model with all available API properties listed there. Each repository should also have a comment block at the bottom of the class that defines the array variables for swagger.

## Defining Routes

When you want to create a new set of routes there are a number of steps that you should do. Inside of the HTTP namespace you will find that there are two very important directories `Core` and `V1`. Each of these directories contain a set of controllers. The controllers in `Core` are all abstract, and are not meant to be implemented directly. The controllers in `V1` are the controllers that should be implemented for the API, and the ones in this project are simple extensions of the ones in the `Core` directory. 

The best practice is to have all of the controllers in the `Core` directory to have the majority of the required implementation with the most up to date manner of which you want to achieve your implementation. This means that your highest API version number should in most cases simply extend the abstract controllers in the core. The purpose of doing this is to make the introduction of backwards incompatible changes incredibly simple to do. 

When introducing a backwards incompatible change the first step would do would be to copy your current routes into a new namespace, copy over a new implementation of the current routes file, and then define the new group within the RouteServiceProvider. Once this is complete you can then take the old implementation, which should still be within the `Core` namespace and put the deprecated functionality into any old route groups that existed before the change. 

Following these steps should at all times keep the most recent version of routes as simple as possible while progressively adding to the complexity of older route groups for the purpose of legacy support.
