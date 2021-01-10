<?php

use App\Models\NewsArticle;
use Faker\Generator as Faker;

$factory->define(NewsArticle::class, function (Faker $faker) {
    return [
        'id'             => $faker->numberBetween(1,1000),
        'title'          => $faker->title(),
        'url'            => $faker->url(),
        'post_time'      => now(),
        'created_at'     => now(),
        'updated_at'     => now(),
    ];
});
