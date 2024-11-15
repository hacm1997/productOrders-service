<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph,
        'price' => $faker->randomFloat(2, 10, 200),
        'stock' => $faker->numberBetween(1, 100),
    ];
});
