<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TechnicalSpecsSampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'spec_name',
            'qty',
            'unit_price',
            'total_price',
            'erp_code',
        ];
    }

    public function array(): array
    {
        // Optional example row; leave [] if you want only headers
        return [
            // ['Pen (blue ink)', 10, 20, 200, 'ERP-001'],
        ];
    }
}
