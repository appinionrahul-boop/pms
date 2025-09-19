<?php

namespace App\Imports;

use App\Models\Package;
use App\Models\ProcurementMethod;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class PackagesImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    public function model(array $row)
    {
        // Normalize keys & values
        $get = function(array $r, array $keys) {
            foreach ($keys as $k) {
                if (array_key_exists($k, $r) && trim((string)$r[$k]) !== '') {
                    return trim((string)$r[$k]);
                }
            }
            return null;
        };

        $packageNo = $get($row, ['package_no', 'package no', 'package no.', 'Package No', 'Package No.']);
        if (!$packageNo) {
            // skip silently; validation() will also catch it
            return null;
        }

        $description = $get($row, ['description', 'desc']);
        $methodName  = $get($row, ['procurement_method', 'method']);
        $costStr     = $get($row, ['estimated_cost_bdt', 'estimated cost (bdt)', 'estimated cost']);

        $method = $methodName
            ? ProcurementMethod::whereRaw('LOWER(name) = ?', [strtolower($methodName)])->first()
            : null;

        // Generate unique 6-digit package_id
        do {
            $pid = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Package::where('package_id', $pid)->exists());

        return new Package([
            'package_id'            => $pid,
            'package_no'            => $packageNo,
            'description'           => $description,
            'procurement_method_id' => $method?->id,
            'estimated_cost_bdt'    => is_numeric($costStr) ? (float)$costStr : null,
        ]);
    }

    public function rules(): array
    {
        // Validation based on normalized keys
        return [
            '*.package_no' => ['required', 'string', 'max:50', 'distinct', 'unique:packages,package_no'],
            // optional: ensure numeric
            '*.estimated_cost_bdt' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.package_no.required' => 'package_no is required.',
            '*.package_no.unique'   => 'package_no must be unique.',
        ];
    }
}
