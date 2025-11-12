<?php

use Faker\Generator as Faker;
use App\Sucursal;
use App\Empleado;

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

$factory->define(App\Cliente::class, function (Faker $faker) {
    return [
        'nombre_completo' => $faker->word,
        'dni' => $faker->unique()->name,
        'id_empleado' => Empleado::all()->random()->id_empleado,
        'id_sucursal' => Sucursal::all()->random()->id_sucursal
    ];
});
