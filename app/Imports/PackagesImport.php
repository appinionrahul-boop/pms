<?php

namespace App\Imports;

use App\Models\Package;
use App\Models\ProcurementMethod;
use Illuminate\Validation\Rule; // ← add this
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class PackagesImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    public function model(array $row)
    {
        $method = null;
        if (!empty($row['procurement_method'])) {
            $method = ProcurementMethod::whereRaw('LOWER(name) = ?', [strtolower((string) $row['procurement_method'])])->first();
        }

        // unique 6-digit package_id
        do {
            $pid = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Package::where('package_id', $pid)->exists());

        return new Package([
            'package_id'            => $pid,
            // ↓ cast to string so numbers or text both work
            'package_no'            => trim((string) $row['package_no']),
            'description'           => $row['description'] ?? null,
            'procurement_method_id' => $method?->id,
            'estimated_cost_bdt'    => isset($row['estimated_cost_bdt']) && is_numeric($row['estimated_cost_bdt'])
                                        ? (float) $row['estimated_cost_bdt'] : null,
        ]);
    }

    public function rules(): array
    {
        return [
            // ↓ removed 'string' so it accepts numbers or text; still unique/max/duplicate-in-sheet checks
            '*.package_no'         => ['required', 'max:50', 'distinct', Rule::unique('packages','package_no')],
            '*.estimated_cost_bdt' => ['nullable', 'min:0'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.package_no.required' => 'package_no is required.',
            '*.package_no.unique'   => 'package_no must be unique.',
            '*.package_no.distinct' => 'Duplicate package_no in the sheet.',
        ];
    }
}
