<?php

use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Photo::class, function (Faker\Generator $faker) {
    $path = '/images/' . sha1(time()) . '.jpg';

    return [
        'name' => pathinfo($path, PATHINFO_BASENAME),
        'path' => $path,
        'album_id' => factory(App\Album::class)->create()->id
    ];
});

$factory->define(App\Album::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->unique()->word,
        'user_id' => factory(App\User::class)->create()->id
    ];
});

$factory->define(App\Thumbnail::class, function (Faker\Generator $faker) {
    $path = '/images/' . sha1(time()) . '.jpg';

    return [
        'name' => pathinfo($path, PATHINFO_BASENAME),
        'path' => $path,
        'width' => 400,
        'height' => 400,
        'photo_id' => factory(App\Photo::class)->create()->id
    ];
});