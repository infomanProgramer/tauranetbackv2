<?php

use Faker\Generator as Faker;
use App\Producto;
use App\VentaProducto;

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

$factory->define(App\ProductoVendido::class, function (Faker $faker) {
    return [
        'cantidad' => $faker->numberBetween($min = 1, $max = 200),
        'importe' => $faker->randomFloat(3, 10, 200),
        'id_producto' => Producto::all()->random()->id_producto,
        'id_venta_producto' => VentaProducto::all()->random()->id_venta_producto,
    ];
});
