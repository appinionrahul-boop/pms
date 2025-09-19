<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovingAuthority;

class ApprovingAuthoritySeeder extends Seeder
{
    public function run(): void
    {
        $authorities = [
            ['name' => 'SE',  'code' => 'SE'],
            ['name' => 'CFO', 'code' => 'CFO'],
            ['name' => 'MD',  'code' => 'MD'],
            ['name' => 'PM',  'code' => 'PM'],
            ['name' => 'CE',  'code' => 'CE'],
        ];

        foreach ($authorities as $auth) {
            ApprovingAuthority::firstOrCreate(
                ['name' => $auth['name']],
                ['code' => $auth['code']]
            );
        }
    }
}
