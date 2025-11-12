<?php

use Illuminate\Database\Seeder;
use App\Carta;

class CartaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Carta::class, 50)->create();
    }
}
