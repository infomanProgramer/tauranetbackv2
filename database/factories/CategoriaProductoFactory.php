<?php

use Faker\Generator as Faker;
use App\CategoriaProducto;
use App\Administrador;
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

$factory->define(App\CategoriaProducto::class, function (Faker $faker) {
    return [
        'nombre' => $faker->word,
        'descripcion' => $faker->paragraph(2),
        'estado' => $faker->randomElement([CategoriaProducto::CATEGORIA_PRODUCTO_ACTIVO, CategoriaProducto::CATEGORIA_PRODUCTO_NO_ACTIVO]),
        'id_restaurant' => Restaurant::all()->random()->id_restaurant,
        'id_administrador' => Administrador::all()->random()->id_administrador
    ];
});
