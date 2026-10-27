<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,     // admin account (never deletes a user)
            ContentSeeder::class,   // default settings/about/services/testimonials (only-if-empty)
            DemoProjectsSeeder::class, // 9 sample projects (skipped in production unless SEED_DEMO=true)
        ]);
    }
}
