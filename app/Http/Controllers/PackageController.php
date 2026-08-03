<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\ProcurementMethod; 
use App\Imports\PackagesImport;
use App\Imports\PackagesSheetReader;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PackagesExport;
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Exports\PackagesSampleExport;
use Maatwebsite\Excel\Validators\ValidationException;


class PackageController extends Controller
{
    /**
     * Headings a bulk-upload sheet must carry. description and assigned_officer
     * are optional, so they are not listed here.
     */
    private const REQUIRED_COLUMNS = [
        'package_no',
        'procurement_method',
        'estimated_cost_bdt',
        'fiscal_year',
    ];

    /**
     * Fiscal year options: previous, current and next FY only. Bangladesh FY
     * runs July–June, so the current FY starts this year from July onward,
     * else last year.
     */
    public static function fiscalYearOptions(): array
    {
        $now = now();
        $currentStart = $now->month >= 7 ? $now->year : $now->year - 1;

        $options = [];
        for ($y = $currentStart - 1; $y <= $currentStart + 1; $y++) {
            $options[] = $y . '-' . substr((string) ($y + 1), -2);
        }

        return $options;
    }

    /**
     * Fiscal years offered in filter dropdowns: the 3-year window plus any
     * value already saved on a package, newest first.
     */
    public static function fiscalYearFilterOptions(): array
    {
        $saved = Package::query()
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->pluck('fiscal_year')
            ->all();

        $options = array_unique(array_merge(self::fiscalYearOptions(), $saved));
        rsort($options);

        return array_values($options);
    }

    /**
     * Show list of packages (APP Management).
     */
    public function index(Request $request)
    {
        $search     = $request->string('q')->trim();
        $officerId  = $request->input('officer_id');
        $fiscalYear = $request->input('fiscal_year');

        $packages = \App\Models\Package::query()
            ->with(['method', 'assignedOfficer'])
            ->whereDoesntHave('requisitions')              // ← hides packages that already have a requisition
            ->when($search, fn($q)=> $q->where(function($qq) use($search){
                $qq->where('package_no','like',"%$search%")
                ->orWhere('description','like',"%$search%")
                ->orWhere('package_id','like',"%$search%");
            }))
            ->when($officerId, fn($q) => $q->where('assigned_officer_id', $officerId))
            ->when($fiscalYear, fn($q) => $q->where('fiscal_year', $fiscalYear))
            ->orderByDesc('created_at')          // newest first
            ->orderByDesc('id')                  // tiebreak for rows saved in the same second
            ->paginate(5)
            ->withQueryString();

        $officers    = \App\Models\Officer::orderBy('name')->get();
        $fiscalYears = self::fiscalYearFilterOptions();

        return view('packages.index', compact('packages','search','officers','fiscalYears'));
    }

    /**
     * Bulk Excel upload.
     */
    public function bulkUpload(Request $request)
    {
        // 'txt' is needed because a plain .csv is often detected as text/plain,
        // which makes a bare mimes:csv rule reject a perfectly valid sheet.
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [], ['file' => 'Excel file']);

        $file = $request->file('file');

        // Read the sheet once up-front so every bad row can be reported together.
        // The importer itself inserts row-by-row and aborts on the first failure,
        // which would otherwise make the user fix and re-upload one row at a time.
        try {
            $sheet = Excel::toArray(new PackagesSheetReader, $file)[0] ?? [];
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['Could not read the file: ' . $e->getMessage()]);
        }

        if (empty($sheet)) {
            return back()->withErrors(['The file has no data rows below the heading row.']);
        }

        $missingColumns = array_values(array_diff(self::REQUIRED_COLUMNS, array_keys($sheet[0])));

        if ($missingColumns) {
            return back()->withErrors([
                'Missing column(s): ' . implode(', ', $missingColumns) . '. The first row must contain the headings: '
                . 'package_no, description, procurement_method, estimated_cost_bdt, assigned_officer, fiscal_year. '
                . 'Use the Sample Excel button for the exact format.',
            ]);
        }

        $errors = $this->findSheetProblems($sheet);

        // Nothing is inserted unless every row passes — the user fixes the file and re-uploads.
        if ($errors) {
            array_unshift($errors, 'No packages were imported. Fix the following and upload the file again.');

            return back()->withErrors($errors);
        }

        $before = Package::count();

        try {
            Excel::import(new PackagesImport, $file);
        } catch (ValidationException $e) {
            // Row numbers are sheet row numbers (row 1 is the heading row).
            $failures = collect($e->failures())
                ->map(fn ($f) => "Row {$f->row()}: " . implode('; ', $f->errors()))
                ->unique()->values()->all();

            return back()->withErrors($failures ?: ['The file could not be imported.']);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['Could not import the file: ' . $e->getMessage()]);
        }

        $imported = Package::count() - $before;

        return redirect()->route('packages.index')
            ->with('success', "{$imported} package(s) imported successfully.")
            ->with('warnings', $this->findSheetWarnings($sheet));
    }

    /**
     * Blocking problems in the uploaded sheet — every offending row at once.
     * Sheet row number is index + 2, because row 1 holds the headings.
     *
     * Every column is mandatory except description and assigned_officer.
     */
    private function findSheetProblems(array $sheet): array
    {
        $errors  = [];
        $seenAt  = [];
        $missing = [];
        $badMethod = $badCost = $badFy = [];

        $methods = ProcurementMethod::pluck('name')
            ->map(fn ($n) => mb_strtolower(trim($n)))->all();

        foreach ($sheet as $i => $row) {
            $rowNo = $i + 2;

            // Entirely blank rows (trailing rows in the sheet) are skipped by the
            // importer as well, so they are not treated as missing data.
            if (self::rowIsBlank($row)) {
                continue;
            }

            $no     = trim((string) ($row['package_no'] ?? ''));
            $method = trim((string) ($row['procurement_method'] ?? ''));
            $cost   = trim((string) ($row['estimated_cost_bdt'] ?? ''));
            $fy     = trim((string) ($row['fiscal_year'] ?? ''));

            // Package Number
            if ($no === '') {
                $missing[$rowNo][] = 'Package Number';
            } elseif (mb_strlen($no) > 50) {
                $errors[] = "Row {$rowNo}: Package Number is longer than 50 characters.";
            } else {
                $seenAt[$no][] = $rowNo;
            }

            // Procurement Method — must match one of the configured methods
            if ($method === '') {
                $missing[$rowNo][] = 'Procurement Method';
            } elseif (!in_array(mb_strtolower($method), $methods, true)) {
                $badMethod[] = "{$rowNo} (\"{$method}\")";
            }

            // Estimated Cost (BDT)
            if ($cost === '') {
                $missing[$rowNo][] = 'Estimated Cost (BDT)';
            } elseif (!is_numeric($cost) || (float) $cost < 0) {
                $badCost[] = "{$rowNo} (\"{$cost}\")";
            }

            // Fiscal Year
            if ($fy === '') {
                $missing[$rowNo][] = 'Fiscal Year';
            } elseif (PackagesImport::normalizeFiscalYear($fy) === null) {
                $badFy[] = "{$rowNo} (\"{$fy}\")";
            }
        }

        foreach ($missing as $rowNo => $fields) {
            $errors[] = sprintf(
                'Row %d: %s %s required.',
                $rowNo,
                implode(', ', $fields),
                count($fields) > 1 ? 'are' : 'is'
            );
        }

        if ($badMethod) {
            $errors[] = 'Procurement Method is not recognised on row(s) ' . implode(', ', $badMethod)
                . '. Valid methods: ' . ProcurementMethod::orderBy('name')->pluck('name')->implode(', ') . '.';
        }
        if ($badCost) {
            $errors[] = 'Estimated Cost (BDT) must be a number of 0 or more on row(s) '
                . implode(', ', $badCost) . '.';
        }
        if ($badFy) {
            $errors[] = 'Fiscal Year is not recognised on row(s) ' . implode(', ', $badFy)
                . '. Use a format like 2025-26.';
        }

        // Already saved in the system.
        $existing = Package::whereIn('package_no', array_keys($seenAt))
            ->pluck('package_no')->all();

        foreach ($existing as $no) {
            $errors[] = sprintf(
                'Package Number "%s" already exists (row %s).',
                $no,
                implode(', ', $seenAt[$no])
            );
        }

        // Repeated inside the uploaded file itself.
        foreach ($seenAt as $no => $rows) {
            if (count($rows) > 1 && !in_array($no, $existing, true)) {
                $errors[] = sprintf(
                    'Package Number "%s" already exists — it is repeated on rows %s of the file.',
                    $no,
                    implode(', ', $rows)
                );
            }
        }

        return $errors;
    }

    /**
     * Non-blocking notices. Assigned Person is optional — a blank cell, or a name
     * that matches nobody in the officers list, imports as Unassigned.
     */
    private function findSheetWarnings(array $sheet): array
    {
        $warnings  = [];
        $unmatched = $unassigned = [];
        $importer  = new PackagesImport;

        foreach ($sheet as $i => $row) {
            if (self::rowIsBlank($row)) {
                continue;
            }

            $rowNo   = $i + 2;
            $officer = trim((string) ($row['assigned_officer'] ?? ''));

            if ($officer === '') {
                $unassigned[] = $rowNo;
            } elseif ($importer->resolveOfficerId($officer) === null) {
                $unmatched[] = "{$rowNo} (\"{$officer}\")";
            }
        }

        if ($unassigned) {
            $warnings[] = 'Assigned Person is empty on row(s) ' . implode(', ', $unassigned)
                . ' — imported as Unassigned.';
        }
        if ($unmatched) {
            $warnings[] = 'Assigned Person not recognised on row(s) ' . implode(', ', $unmatched)
                . ' — imported as Unassigned.';
        }

        return $warnings;
    }

    /**
     * True when every cell of a sheet row is empty.
     */
    private static function rowIsBlank(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) ($value ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }
    // --- Stubs for Add New / Edit ---

   public function create()
    {
        // Generate a unique 6-digit ID for display (and submit as readonly)
        // Ensures no collision with existing package_id values.
        do {
            $generatedId = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Package::where('package_id', $generatedId)->exists());

        $methods  = ProcurementMethod::orderBy('name')->get();
        $officers = \App\Models\Officer::assignable();   // inactive officers are not offered
        $fiscalYears = self::fiscalYearOptions();

        return view('packages.create', compact('methods', 'generatedId', 'officers', 'fiscalYears'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'package_id'           => 'required|size:6|unique:packages,package_id',
        'package_no'           => 'required|string|max:50|unique:packages,package_no',
        'description'          => 'nullable|string|max:1000',
        'procurement_method_id'=> 'nullable|exists:procurement_methods,id',
        'estimated_cost_bdt'   => 'nullable|numeric|min:0',
        'assigned_officer_id'  => 'nullable|exists:officers,id',
        'fiscal_year'          => ['nullable', \Illuminate\Validation\Rule::in(self::fiscalYearOptions())],
    ], [
        'package_no.unique'   => 'Package Number already exists.',
        'package_no.required' => 'Package Number is required.',
    ]);

    \App\Models\Package::create($validated);

    return redirect()
        ->route('packages.index')
        ->with('success', 'Package created successfully.');
}


    public function edit(Package $package)
    {
        $methods  = \App\Models\ProcurementMethod::orderBy('name')->get();
        // active officers, plus whoever is already assigned so an edit does not clear them
        $officers = \App\Models\Officer::assignable($package->assigned_officer_id);
        $fiscalYears = self::fiscalYearOptions();
        // keep an already-saved fiscal year selectable even once it leaves the 3-year window
        if ($package->fiscal_year && !in_array($package->fiscal_year, $fiscalYears, true)) {
            array_unshift($fiscalYears, $package->fiscal_year);
        }
        return view('packages.edit', compact('package', 'methods', 'officers', 'fiscalYears'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'package_no'            => 'required|string|max:50|unique:packages,package_no,' . $package->id,
            'description'           => 'nullable|string|max:1000',
            'procurement_method_id' => 'nullable|exists:procurement_methods,id',
            'estimated_cost_bdt'    => 'nullable|numeric|min:0',
            'assigned_officer_id'   => 'nullable|exists:officers,id',
            'fiscal_year'           => ['nullable', \Illuminate\Validation\Rule::in(array_merge(
                self::fiscalYearOptions(),
                $package->fiscal_year ? [$package->fiscal_year] : []
            ))],
        ], [
            'package_no.unique'   => 'Package Number already exists.',
            'package_no.required' => 'Package Number is required.',
        ]);

        $package->update($validated);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Package updated successfully.');
    }


    public function destroy(Package $package)
    {
        $package->delete();
        return back()->with('success', 'Package deleted.');
    }

    public function all(Request $request)
    {
        $start      = $request->input('start');
        $end        = $request->input('end');
        $officerId  = $request->input('officer_id');
        $fiscalYear = $request->input('fiscal_year');

        $q = \DB::table('packages as p')
            ->leftJoin('procurement_methods as m', 'm.id', '=', 'p.procurement_method_id')
            ->leftJoin('officers as u', 'u.id', '=', 'p.assigned_officer_id')
            ->select([
                'p.package_id',
                'p.package_no',
                'p.description',
                'p.estimated_cost_bdt',
                'p.fiscal_year',
                'p.created_at',
                'm.name as procurement_method_name',
                'u.name as assigned_officer_name',
            ]);

        if ($start) {
            $q->whereDate('p.created_at', '>=', $start);
        }
        if ($end) {
            $q->whereDate('p.created_at', '<=', $end);
        }
        if ($officerId) {
            $q->where('p.assigned_officer_id', $officerId);
        }
        if ($fiscalYear) {
            $q->where('p.fiscal_year', $fiscalYear);
        }

        $packages    = $q->orderByDesc('p.created_at')->orderByDesc('p.id')->get();
        $officers    = \App\Models\Officer::orderBy('name')->get();
        $fiscalYears = self::fiscalYearFilterOptions();

        return view('packages.all', compact('packages', 'officers', 'fiscalYears'));
    }

     public function downloadExcel(Request $request)
    {
        $start      = $request->input('start');
        $end        = $request->input('end');
        $officerId  = $request->input('officer_id');
        $fiscalYear = $request->input('fiscal_year');

        return Excel::download(
            new PackagesExport($start, $end, $officerId, $fiscalYear),
            'packages_'.now()->format('Ymd_His').'.xlsx'
        );
    }

        public function sampleTemplate()
    {
        return Excel::download(new PackagesSampleExport, 'packages_sample.xlsx');
    }

}
