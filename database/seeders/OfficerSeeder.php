<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Officer;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officers = [
            'Farhadul Islam',
            'Shaharia Amin',
            'Fahim Abdullah',
            'Kiran',
            'Sadman',
        ];

        foreach ($officers as $name) {
            Officer::create([
                'name' => $name,
            ]);
        }
    }
}
