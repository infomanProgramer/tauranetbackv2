<?php

use Illuminate\Database\Seeder;
use App\Restaurant;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Restaurant::class, 25)->create();
    }
}
