<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcurementType;

class ProcurementTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Regular Goods',      'code' => 'REG'],
            ['name' => 'Emergency',          'code' => 'EMG'],
            ['name' => 'Class - A',          'code' => 'A'],
            ['name' => 'Class - B',          'code' => 'B'],
            ['name' => 'Class – C',          'code' => 'C'],
            ['name' => 'Works',              'code' => 'WRK'],
            ['name' => 'Local Spare Parts',  'code' => 'LSP'],
            ['name' => 'Consultancy',        'code' => 'CONS'],
            ['name' => 'Non- Consultancy',   'code' => 'NCONS'],
        ];

        foreach ($types as $type) {
            ProcurementType::firstOrCreate(
                ['name' => $type['name']],
                ['code' => $type['code']]
            );
        }
    }
}
