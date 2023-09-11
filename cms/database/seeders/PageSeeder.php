<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PageSeeder extends Seeder
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
            DB::table('pages')->insert([
                'title' => $faker->sentence,
                'content' => $faker->paragraph(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
