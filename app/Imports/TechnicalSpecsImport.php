<?php

namespace App\Imports;

use App\Models\TechnicalSpec;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class TechnicalSpecsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    protected int $packageId;

    public function __construct(int $packageId)
    {
        $this->packageId = $packageId;
    }

    public function model(array $row)
    {
        // Expecting headers: erp_code, spec_name, specification, quantity, unit_price
        $erp           = isset($row['erp_code']) ? trim((string)$row['erp_code']) : null;
        $name          = isset($row['spec_name']) ? trim((string)$row['spec_name']) : null;
        $specification = isset($row['specification']) ? trim((string)$row['specification']) : null;

        $qty  = isset($row['quantity']) && is_numeric($row['quantity']) ? (float)$row['quantity'] : 0;
        $unit = isset($row['unit_price']) && is_numeric($row['unit_price']) ? (float)$row['unit_price'] : 0;

        $total = $qty > 0 && $unit > 0 ? ($qty * $unit) : 0;

        return new TechnicalSpec([
            'package_id'      => $this->packageId,
            'spec_name'       => $name,
            'specification'   => $specification,
            'quantity'        => $qty,
            'unit_price_bdt'  => $unit,
            'total_price_bdt' => $total,        // ← auto calculated
            'erp_code'        => $erp,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.spec_name'     => ['required','string','max:255'],
            '*.specification' => ['nullable','string','max:5000'],
            '*.quantity'      => ['nullable','numeric','min:0'],
            '*.unit_price'    => ['nullable','numeric','min:0'],
            '*.erp_code'      => ['nullable','string','max:100'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.spec_name.required' => 'Spec Name is required.',
            '*.quantity.numeric'   => 'Quantity must be a number.',
            '*.unit_price.numeric' => 'Unit Price must be a number.',
        ];
    }
}
