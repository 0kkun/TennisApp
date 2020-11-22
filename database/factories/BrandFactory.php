<?php

use App\Models\FavoriteBrand;
use Faker\Generator as Faker;

$factory->define(FavoriteBrand::class, function (Faker $faker) {
    return [
        'id'             => $faker->numberBetween(1,1000),
        'name_jp'        => $faker->name(),
        'name_en'        => $faker->name(),
        'country'        => $faker->country(),
    ];
});
