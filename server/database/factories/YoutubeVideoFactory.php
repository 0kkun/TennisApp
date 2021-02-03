<?php

use Faker\Generator as Faker;
use App\Models\YoutubeVideo;

$factory->define(YoutubeVideo::class, function (Faker $faker) {
    return [
        'title'      => $faker->title(),
        'url'        => $faker->url(),
        'post_time'  => now(),
        'player_id'  => $faker->numberBetween($min=1, $max=1000),
        'updated_at' => now(),
    ];
});
