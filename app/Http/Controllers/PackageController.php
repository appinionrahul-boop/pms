<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\ProcurementMethod; 
use App\Imports\PackagesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PackagesExport;
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Exports\PackagesSampleExport;


class PackageController extends Controller
{
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
     * Show list of packages (APP Management).
     */
    public function index(Request $request)
    {
        $search    = $request->string('q')->trim();
        $officerId = $request->input('officer_id');

        $packages = \App\Models\Package::query()
            ->with(['method', 'assignedOfficer'])
            ->whereDoesntHave('requisitions')              // ← hides packages that already have a requisition
            ->when($search, fn($q)=> $q->where(function($qq) use($search){
                $qq->where('package_no','like',"%$search%")
                ->orWhere('description','like',"%$search%")
                ->orWhere('package_id','like',"%$search%");
            }))
            ->when($officerId, fn($q) => $q->where('assigned_officer_id', $officerId))
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        $officers = \App\Models\Officer::orderBy('name')->get();

        return view('packages.index', compact('packages','search','officers'));
    }

    /**
     * Bulk Excel upload (stub — implement later).
     */
    
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new PackagesImport, $request->file('file'));
        } catch (ValidationException $e) {
            $failures = $e->failures()->map(function($f) {
                return "Row {$f->row()}: " . implode('; ', $f->errors());
            })->toArray();

            return back()->withErrors($failures)->withInput();
        }

        return redirect()->route('packages.index')->with('success', 'Packages imported successfully.');
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
        $officers = \App\Models\Officer::orderBy('name')->get();
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
    ]);

    \App\Models\Package::create($validated);

    return redirect()
        ->route('packages.index')
        ->with('success', 'Package created successfully.');
}


    public function edit(Package $package)
    {
        $methods  = \App\Models\ProcurementMethod::orderBy('name')->get();
        $officers = \App\Models\Officer::orderBy('name')->get();
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
        $start     = $request->input('start');
        $end       = $request->input('end');
        $officerId = $request->input('officer_id');

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

        $packages = $q->orderByDesc('p.created_at')->get();
        $officers = \App\Models\Officer::orderBy('name')->get();

        return view('packages.all', compact('packages', 'officers'));
    }
    
     public function downloadExcel(Request $request)
    {
        $start     = $request->input('start');
        $end       = $request->input('end');
        $officerId = $request->input('officer_id');

        return Excel::download(
            new PackagesExport($start, $end, $officerId),
            'packages_'.now()->format('Ymd_His').'.xlsx'
        );
    }

        public function sampleTemplate()
    {
        return Excel::download(new PackagesSampleExport, 'packages_sample.xlsx');
    }

}
