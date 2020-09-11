<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Cover;
use Faker\Generator as Faker;

$factory->define(Cover::class, function (Faker $faker) {
    return [
        'avatar' => $faker->text,
        'file' => $faker->text,
        'name' => $faker->name,
        'category' => $faker->word,
    ];
});
