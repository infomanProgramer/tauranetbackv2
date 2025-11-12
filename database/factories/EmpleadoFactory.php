<?php

use Faker\Generator as Faker;
use App\Administrador;
use App\Sucursal;
use App\CategoriaEmpleado;
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

$factory->define(App\Empleado::class, function (Faker $faker) {
    return [
        'nombres' => $faker->word,
        'paterno' => $faker->word,
        'materno' => $faker->word,
        'dni' => $faker->word,
        'nombre_usuario' => $faker->word,
        'correo' => $faker->unique()->safeEmail,
        'pass' => bcrypt('secret'),
        'estado' => $faker->randomElement([Empleado::USUARIO_ACTIVO, Empleado::USUARIO_NO_ACTIVO]),
        'fecha_nac' => date("Y-m-d", mt_rand(0, 500000000)),//$faker->unixTime($max = 'now'),
        'sexo' => $faker->randomElement([Empleado::USUARIO_MASCULINO, Empleado::USUARIO_FEMENINO]),
        'fotoperfil_url' => $faker->word,
        'sueldo' => $faker->randomFloat(2, 2000, 5000),
        'fecha_inicio' => date("Y-m-d", mt_rand(0, 500000000)),
        'id_administrador' => Administrador::all()->random()->id_administrador,
        'id_categoria_empleado' => CategoriaEmpleado::all()->random()->id_categoria_empleado,
        'id_sucursal' => Sucursal::all()->random()->id_sucursal,
    ];
});
