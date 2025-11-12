<?php

use Illuminate\Database\Seeder;
use App\Sucursal;

class SucursalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Sucursal::class, 200)->create();
    }
}
