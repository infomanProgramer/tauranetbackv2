<?php

use Illuminate\Database\Seeder;
use App\VentaProducto;

class VentaProductoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(VentaProducto::class, 2000)->create();
    }
}
