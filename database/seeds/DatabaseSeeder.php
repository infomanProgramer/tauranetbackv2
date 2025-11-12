<?php

use Illuminate\Database\Seeder;
use App\Superadministrador;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(Superadministrador::class);
    }
}
