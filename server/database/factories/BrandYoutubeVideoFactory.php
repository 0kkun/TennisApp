<?php

use Faker\Generator as Faker;
use App\Models\BrandYoutubeVideo;

$factory->define(BrandYoutubeVideo::class, function (Faker $faker) {
    return [
        'title'      => $faker->title(),
        'url'        => $faker->url(),
        'post_time'  => now(),
        'brand_id'  => $faker->numberBetween($min=1, $max=1000),
        'updated_at' => now(),
    ];
});
