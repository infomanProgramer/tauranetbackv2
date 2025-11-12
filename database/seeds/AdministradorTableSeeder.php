<?php

use Illuminate\Database\Seeder;
use App\Administrador;

class AdministradorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Administrador::class, 25)->create();
    }
}
