<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LcStatus;

class LcStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Opened',       'code' => 'OPEN'],
            ['name' => 'Draft Issued', 'code' => 'DRAFT'],
            ['name' => 'Sent to Bank', 'code' => 'SENT'],
        ];

        foreach ($statuses as $status) {
            LcStatus::firstOrCreate(
                ['name' => $status['name']],
                ['code' => $status['code']]
            );
        }
    }
}
