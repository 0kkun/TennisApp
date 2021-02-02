<?php

use Faker\Generator as Faker;
use App\Models\BrandNewsArticle;

$factory->define(BrandNewsArticle::class, function (Faker $faker) {
    return [
        'title'          => $faker->title(),
        'url'            => $faker->url(),
        'post_time'      => now(),
        'brand_name'     => $faker->name(),
        'updated_at'     => now(),
        'created_at'     => now(),
    ];
});
