<?php

use Faker\Generator as Faker;
use App\Models\Ranking;

$factory->define(Ranking::class, function (Faker $faker) {
    return [
        'rank'                      => $faker->numberBetween($min=1, $max=1000),
        'most_highest'              => $faker->numberBetween($min=1, $max=1000),
        'name_en'                   => $faker->name(),
        'name_jp'                   => $faker->name(),
        'age'                       => $faker->numberBetween($min=1, $max=50),
        'country'                   => $faker->country(),
        'point'                     => $faker->numberBetween($min=100, $max=10000),
        'rank_change'               => $faker->numberBetween($min=1, $max=50),
        'point_change'              => $faker->numberBetween($min=100, $max=10000),
        'current_tour_result_en'    => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'current_tour_result_jp'    => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'pre_tour_result_en'        => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'pre_tour_result_jp'        => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'next_point'                => $faker->numberBetween($min=1, $max=1500),
        'max_point'                 => $faker->numberBetween($min=150, $max=1500),
        'updated_at'                => now(),
    ];
});
