<?php

use Faker\Generator as Faker;
use App\Superadministrador;
use App\Restaurant;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Restaurant::class, function (Faker $faker) {
    return [
        'nombre' => $faker->unique()->word,
        'estado' => $faker->randomElement([Restaurant::RESTAURANT_ACTIVO, Restaurant::RESTAURANT_NO_ACTIVO]),
        'descripcion' => $faker->paragraph(1),
        'id_superadministrador' => Superadministrador::all()->random()->id_superadministrador
    ];
});
