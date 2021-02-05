<?php

use Faker\Generator as Faker;
use App\Models\PlayersNewsArticle;

$factory->define(PlayersNewsArticle::class, function (Faker $faker) {
    return [
        'title'      => $faker->title(),
        'image'      => $faker->title(),
        'url'        => $faker->url(),
        'post_time'  => now(),
        'vender'     => $faker->title(),
        'updated_at' => now(),
        'created_at' => now()
    ];
});
