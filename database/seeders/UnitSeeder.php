<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'NS', 'code' => 'NS'],
            ['name' => 'KM', 'code' => 'KM'],
            ['name' => 'NT', 'code' => 'NT'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['name' => $unit['name']],
                ['code' => $unit['code']]
            );
        }
    }
}
