<?php

use App\Models\Brand;
use Faker\Generator as Faker;

$factory->define(Brand::class, function (Faker $faker) {
    return [
        'id'             => $faker->numberBetween(1,1000),
        'user_id'        => $faker->randomNumber(),
        'brand_id'       => $faker->randomNumber(),
    ];
});
