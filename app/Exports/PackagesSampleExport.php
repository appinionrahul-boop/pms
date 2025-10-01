<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PackagesSampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'package_no',
            'description',
            'procurement_method',     // e.g., "Open Tender", "Direct Purchase"
            'estimated_cost_bdt',
        ];
    }

    public function array(): array
    {
        // No rows — just headers. If you want a demo row, add one here.
        return [];
    }
}
