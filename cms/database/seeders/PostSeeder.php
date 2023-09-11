<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();

        foreach (range(1, 100) as $index) {
            DB::table('posts')->insert([
                'title' => $faker->sentence,
                'content' => $faker->paragraph(10),
                'category_id' => $faker->numberBetween(1, 100), // Assuming 100 categories exist
                'user_id' => 1, // Assuming 10 user exist
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
