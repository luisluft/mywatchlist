<?php

use Illuminate\Database\Seeder;

class MoviesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 3 movies for each profile
        for ($i = 1; $i <= 3; $i++) {
            factory('App\Movie',3)->create(['profile_id'=>$i]);
        }
    }
}
