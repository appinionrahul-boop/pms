<?php

namespace App\Exports;

use App\Models\Package;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PackagesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return Package::query()
            ->select([
                'packages.package_id',
                'packages.package_no',
                'packages.description',
                'procurement_methods.name as procurement_method_name',
                'packages.estimated_cost_bdt'
            ])
            ->leftJoin('procurement_methods', 'packages.procurement_method_id', '=', 'procurement_methods.id')
            ->OrderBy ('packages.id','ASC');
    }

    public function headings(): array
    {
        return [
            'Package ID',
            'Package No',
            'Description',
            'Procurement Method',
            'Estimated Cost (BDT)'
        ];
    }

    public function map($p): array
    {
        return [
            $p->package_id,
            $p->package_no,
            $p->description,
            $p->procurement_method_name,
            $p->estimated_cost_bdt,
        ];
    }
}
