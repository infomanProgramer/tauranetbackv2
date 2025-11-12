<?php

use Faker\Generator as Faker;
use App\Carta;
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

$factory->define(App\SucursalCarta::class, function (Faker $faker) {
    return [
        'id_sucursal' => Sucursal::all()->random()->id_sucursal,
        'id_carta' => Carta::all()->random()->id_carta
    ];
});
