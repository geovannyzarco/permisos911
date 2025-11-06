<?php

namespace Database\Seeders;

use App\Models\UserUnidad;
use Illuminate\Database\Seeder;

class UserUnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserUnidad::factory()
            ->count(5)
            ->create();
    }
}
