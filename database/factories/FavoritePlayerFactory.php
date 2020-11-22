<?php

use Faker\Generator as Faker;
use App\Models\FavoritePlayer;

$factory->define(FavoritePlayer::class, function (Faker $faker) {
    return [
        'id'             => $faker->numberBetween(1,1000),
        'user_id'        => $faker->randomNumber(),
        'player_id'      => $faker->randomNumber(),
    ];
});
