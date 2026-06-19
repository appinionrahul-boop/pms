<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TechnicalSpecsSampleExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        // New order, no total_price column
        return ['erp_code','spec_name','specification','quantity','unit_price'];
    }

    public function array(): array
    {
        return [
            ['ERP-001','Blue Pen','0.7mm ballpoint, blue ink', '100', '10.00'],
            ['ERP-002','A4 Paper','80 GSM white, 500 sheets/ream','20','350.00'],
        ];
    }
}
