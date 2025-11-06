<?php

namespace Database\Seeders;

use App\Models\Compensado;
use Illuminate\Database\Seeder;

class CompensadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Compensado::factory()
            ->count(5)
            ->create();
    }
}
