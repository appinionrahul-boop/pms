<?php

namespace App\Imports;

use App\Models\Package;
use App\Models\ProcurementMethod;
use App\Models\Officer;
use Illuminate\Validation\Rule; // ← add this
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class PackagesImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    /** @var \Illuminate\Support\Collection officers (same list as Add Requisition), for name matching */
    protected $officers;

    public function __construct()
    {
        // Inactive officers are not assignable, so a sheet naming one imports as Unassigned.
        $this->officers = Officer::where('is_active', true)->get(['id', 'name']);
    }

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
            'assigned_officer_id'   => $this->resolveOfficerId($row['assigned_officer'] ?? null),
            'fiscal_year'           => self::normalizeFiscalYear($row['fiscal_year'] ?? null),
        ]);
    }

    /**
     * Accept fiscal years in common shapes — "2025-26", "2025-2026", "2025/26",
     * "FY 2025-26", "25-26" or a bare "2025" — and normalize to "2025-26".
     * Returns null when the value is missing or unrecognizable.
     */
    public static function normalizeFiscalYear($raw): ?string
    {
        $value = strtolower(trim((string) ($raw ?? '')));
        if ($value === '') {
            return null;
        }

        $value = preg_replace('/^fy\s*/', '', $value);           // drop "FY" prefix
        $value = preg_replace('/[\/_.\s]+/', '-', $value);       // unify separators

        if (!preg_match('/^(\d{2}|\d{4})(?:-(\d{2}|\d{4}))?$/', $value, $m)) {
            return null;
        }

        $start = (int) $m[1];
        if ($start < 100) {
            $start += 2000;                                      // "25" → 2025
        }

        if (isset($m[2])) {
            $end = (int) $m[2];
            if ($end < 100) {
                $end += 2000;
            }
            if ($end !== $start + 1) {                           // "2025-28" → reject
                return null;
            }
        }

        return $start . '-' . substr((string) ($start + 1), -2);
    }

    /**
     * Match a sheet-provided officer name against the officers list, tolerating
     * case, spacing, punctuation, honorifics (e.g. "Md."), partial names and
     * small typos. Returns the officer id, or null when nothing matches.
     */
    public function resolveOfficerId($raw): ?int
    {
        $input = $this->normalizeName((string) ($raw ?? ''));
        if ($input === '') {
            return null;
        }

        // 1) exact (normalized) match
        foreach ($this->officers as $u) {
            if ($this->normalizeName($u->name) === $input) {
                return $u->id;
            }
        }

        // 2) containment either way ("Mahfuz Alam" ⊂ "Md Mahfuz Alam")
        $contained = $this->officers->filter(function ($u) use ($input) {
            $name = $this->normalizeName($u->name);
            return str_contains($name, $input) || str_contains($input, $name);
        });
        if ($contained->count() === 1) {
            return $contained->first()->id;
        }

        // 3) closest by levenshtein, within a typo-sized distance
        $bestId = null; $bestDist = PHP_INT_MAX; $ties = 0;
        foreach ($this->officers as $u) {
            $dist = levenshtein($input, $this->normalizeName($u->name));
            if ($dist < $bestDist)      { $bestDist = $dist; $bestId = $u->id; $ties = 0; }
            elseif ($dist === $bestDist) { $ties++; }
        }
        $maxDist = max(2, (int) floor(mb_strlen($input) * 0.3));

        return ($bestDist <= $maxDist && $ties === 0) ? $bestId : null;
    }

    protected function normalizeName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $name); // drop dots, dashes, etc.
        return preg_replace('/\s+/', ' ', $name);
    }

    /**
     * Every column is required except description and assigned_officer.
     * PackageController checks the whole sheet before this runs so the user gets
     * all the bad rows at once; these rules are the last line of defence.
     */
    public function rules(): array
    {
        return [
            // ↓ removed 'string' so it accepts numbers or text; still unique/max/duplicate-in-sheet checks
            '*.package_no'         => ['required', 'max:50', 'distinct', Rule::unique('packages','package_no')],
            '*.procurement_method' => ['required'],
            '*.estimated_cost_bdt' => ['required', 'numeric', 'min:0'],
            '*.fiscal_year'        => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.package_no.required'         => 'Package Number is required.',
            '*.package_no.unique'           => 'Package Number already exists.',
            '*.package_no.distinct'         => 'Package Number already exists — it is repeated in the file.',
            '*.procurement_method.required' => 'Procurement Method is required.',
            '*.estimated_cost_bdt.required' => 'Estimated Cost (BDT) is required.',
            '*.estimated_cost_bdt.numeric'  => 'Estimated Cost (BDT) must be a number.',
            '*.fiscal_year.required'        => 'Fiscal Year is required.',
        ];
    }
}
