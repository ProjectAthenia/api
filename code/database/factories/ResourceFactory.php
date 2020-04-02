<?php
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Resource::class, function (Faker $faker) {
    return [
        'content' => $faker->text,
        'resource_type' => 'user',
        'resource_id' => factory(\App\Models\User\User::class)->create()->id,
    ];
});