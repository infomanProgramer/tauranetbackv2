<?php

use Faker\Generator as Faker;
use App\Superadministrador;
use App\Restaurant;
use App\Sucursal;

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

$factory->define(App\Sucursal::class, function (Faker $faker) {
    return [
        'nombre' => $faker->word,
        'direccion' => $faker->word,
        'descripcion' => $faker->paragraph(2),
        'estado' => $faker->randomElement([Sucursal::SUCURSAL_ACTIVO, Sucursal::SUCURSAL_NO_ACTIVO]),
        'id_restaurant' => Restaurant::all()->random()->id_restaurant,
        'id_superadministrador' => Superadministrador::all()->random()->id_superadministrador
    ];
});
