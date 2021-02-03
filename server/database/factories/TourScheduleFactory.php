<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
use App\Models\TourSchedule;

$factory->define(TourSchedule::class, function (Faker $faker) {
    return [
        'name'       => $faker->title(),
        'location'   => $faker->country(),
        'surface'    => $faker->realText($maxNbChars = 50, $indexSize = 2),
        'category'   => $faker->realText($maxNbChars = 50, $indexSize = 2),
        'year'       => Carbon::parse(now())->format('Y'),
        'start_date' => Carbon::parse(now())->format('Y-m-d'),
        'end_date'   => Carbon::parse(now())->format('Y-m-d'),
        'updated_at' => now(),
    ];
});
