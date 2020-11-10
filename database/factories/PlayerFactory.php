<?php

use Faker\Generator as Faker;
use App\Models\Player;

$factory->define(Player::class, function (Faker $faker) {
    return [
        'name_jp'        => $faker->name(),
        'name_en'        => $faker->name(),
        'country'        => $faker->country(),
        'wiki_url'       => $faker->url(),
        'age'            => $faker->numberBetween($min = 18, $max = 50),
        'created_at'     => now(),
        'updated_at'     => now(),
        'youtube_active' => 0
    ];
});
