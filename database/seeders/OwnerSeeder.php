<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owners\Owner;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        Owner::updateOrCreate(
            ['rut' => '55555555-5'],
            [
                'name'      => 'Test',
                'is_active' => 1,
            ]
        );
    }
}