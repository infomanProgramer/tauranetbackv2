<?php

use Faker\Generator as Faker;
use App\Superadministrador;
use App\Restaurant;
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

$factory->define(App\Administrador::class, function (Faker $faker) {
    return [
        'nombres' => $faker->word,
        'paterno' => $faker->word,
        'materno' => $faker->word,
        'dni' => $faker->word,
        'nombre_usuario' => $faker->word,
        'correo' => $faker->unique()->safeEmail,
        'pass' => bcrypt('secret'),
        'estado' => $faker->randomElement([Administrador::USUARIO_ACTIVO, Administrador::USUARIO_NO_ACTIVO]),
        'fecha_nac' => date("Y-m-d", mt_rand(0, 500000000)),//$faker->unixTime($max = 'now'),
        'sexo' => $faker->randomElement([Administrador::USUARIO_MASCULINO, Administrador::USUARIO_FEMENINO]),
        'fotoperfil_url' => $faker->word,
        'id_superadministrador' => Superadministrador::all()->random()->id_superadministrador,
        'id_restaurant' => Restaurant::all()->random()->id_restaurant,
    ];
});
