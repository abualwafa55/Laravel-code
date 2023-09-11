<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UserSeeder::class); // Call UserSeeder
        $this->call(CategorySeeder::class); // Call CategorySeeder
        $this->call(PageSeeder::class); // Call PageSeeder
        $this->call(PostSeeder::class); // Call PostSeeder

    }
}
