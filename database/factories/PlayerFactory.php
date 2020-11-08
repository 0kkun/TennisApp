<?php

use Faker\Generator as Faker;
use App\Models\Player;

$factory->define(Player::class, function (Faker $faker) {
    return [
        'name_jp' => $faker->name(),
        'name_en' => $faker->name(),
        

    ];
});
