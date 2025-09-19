<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitionStatus;

class RequisitionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Initiate',            'code' => 'INIT', 'color' => 'gray'],
            ['name' => 'Tender Opened',       'code' => 'OPEN', 'color' => 'blue'],
            ['name' => 'Evaluation Completed','code' => 'EVAL', 'color' => 'orange'],
            ['name' => 'Contract Signed',     'code' => 'CNTR', 'color' => 'green'],
            ['name' => 'Delivered',           'code' => 'DLVR', 'color' => 'purple'],
        ];

        foreach ($statuses as $status) {
            RequisitionStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
