<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Carbon;

class PackagesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    public function __construct(?string $start = null, ?string $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function query()
    {
        $q = DB::table('packages as p')
            ->leftJoin('procurement_methods as m', 'm.id', '=', 'p.procurement_method_id')
            ->select([
                'p.package_id',
                'p.package_no',
                'p.description',
                'm.name as procurement_method_name',
                'p.estimated_cost_bdt',
                'p.created_at',
            ]);

        if ($this->start) {
            $q->whereDate('p.created_at', '>=', $this->start);
        }
        if ($this->end) {
            $q->whereDate('p.created_at', '<=', $this->end);
        }

        return $q->orderByDesc('p.created_at');
    }

    public function headings(): array
    {
        return ['Package ID', 'Package No', 'Description', 'Procurement Method', 'Estimated Cost (BDT)', 'Created'];
    }

    public function map($row): array
    {
        return [
            $row->package_id,
            $row->package_no,
            $row->description,
            $row->procurement_method_name ?? '-',
            number_format((float)($row->estimated_cost_bdt ?? 0), 2, '.', ''),
            Carbon::parse($row->created_at)->format('Y-m-d'),
        ];
    }
}
