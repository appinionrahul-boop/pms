<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Boiler',
            'Turbine',
            'I & C',
            'EMD 1',
            'EMD 2',
            'CHP',
            'Chemical',
            'Chemical Maintenance',
            'Coal',
            'Corporate',
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }
    }
}
