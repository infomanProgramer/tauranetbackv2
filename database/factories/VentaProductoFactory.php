<?php

use Faker\Generator as Faker;
use App\Producto;
use App\Carta;
use App\Cliente;
use App\Empleado;
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

$factory->define(App\VentaProducto::class, function (Faker $faker) {
    return [
        'nro_venta' => $faker->unique()->numberBetween($min = 1, $max = 3000),
        'total' => $faker->randomFloat(3, 10, 200),
        'descuento' => $faker->numberBetween($min = 0, $max = 30),
        'estado_venta' => $faker->randomElement(['A','B','C']),
        'fecha' => date("Y-m-d", mt_rand(0, 500000000)),//$faker->unixTime($max = 'now'),
        'id_producto' => Producto::all()->random()->id_producto,
        'id_carta' => Carta::all()->random()->id_carta,
        'id_cliente' => Cliente::all()->random()->id_cliente,
        'id_empleado' => Carta::all()->random()->id_carta,
        'id_sucursal' => Sucursal::all()->random()->id_sucursal,
    ];
});
