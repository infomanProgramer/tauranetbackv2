<?php

use Illuminate\Database\Seeder;
use App\SucursalCarta;

class SucursalCartaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(SucursalCarta::class, 150)->create();
    }
}
