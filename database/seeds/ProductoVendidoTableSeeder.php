<?php

use Illuminate\Database\Seeder;
use App\ProductoVendido;

class ProductoVendidoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ProductoVendido::class, 2500)->create();
    }
}
