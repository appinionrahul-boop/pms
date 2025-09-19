<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcurementMethod;

class ProcurementMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'RFQ', 'code' => 'RFQ'],
            ['name' => 'OTM', 'code' => 'OTM'],
            ['name' => 'DPM', 'code' => 'DPM'],
        ];

        foreach ($methods as $method) {
            ProcurementMethod::firstOrCreate(
                ['name' => $method['name']],
                ['code' => $method['code']]
            );
        }
    }
}
