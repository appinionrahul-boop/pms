<?php

namespace App\Imports;

use App\Models\TechnicalSpec;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TechnicalSpecsImport implements ToModel, WithHeadingRow
{
    public function __construct(public int $packageId) {}

    public function model(array $row)
    {
        $name = trim((string)($row['spec_name'] ?? ''));
        if ($name === '') return null;

        $qty  = is_numeric($row['qty'] ?? null) ? (float)$row['qty'] : null;
        $unit = is_numeric($row['unit_price'] ?? null) ? (float)$row['unit_price'] : null;
        $total= is_numeric($row['total_price'] ?? null) ? (float)$row['total_price'] : ( ($qty && $unit) ? $qty*$unit : null );

        return new TechnicalSpec([
            'package_id'       => $this->packageId,
            'spec_name'        => $name,
            'quantity'         => $qty,
            'unit_price_bdt'   => $unit,
            'total_price_bdt'  => $total,
        ]);
    }
}

