<?php

use Faker\Generator as Faker;
use App\Producto;
use App\Administrador;
use App\Carta;

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

$factory->define(App\Carta::class, function (Faker $faker) {
    return [
        'nombre' => $faker->word,
        'descripcion' => $faker->paragraph(2),
        'dia' => date("Y-m-d", mt_rand(0, 500000000)),//$faker->unixTime($max = 'now'),
        'cantidad' => $faker->numberBetween($min = 50, $max = 200), // 8567
        'id_administrador' => Administrador::all()->random()->id_administrador,
        'estado' => $faker->randomElement([Carta::CARTA_ACTIVO, Carta::CARTA_NO_ACTIVO]),
    ];
});
