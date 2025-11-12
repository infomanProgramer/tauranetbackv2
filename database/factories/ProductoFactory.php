<?php

use Faker\Generator as Faker;
use App\Producto;
use App\CategoriaProducto;
use App\Administrador;

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

$factory->define(App\Producto::class, function (Faker $faker) {
    return [
        'nombre' => $faker->word,
        'precio' => $faker->randomFloat(2, 10, 200),
        'descripcion' => $faker->paragraph(2),
        'fotoproducto_url' => $faker->word,
        'estado' => $faker->randomElement([CategoriaProducto::CATEGORIA_PRODUCTO_ACTIVO, CategoriaProducto::CATEGORIA_PRODUCTO_NO_ACTIVO]),
        'id_categoria_producto' => CategoriaProducto::all()->random()->id_categoria_producto,
        'id_administrador' => Administrador::all()->random()->id_administrador
    ];
});
