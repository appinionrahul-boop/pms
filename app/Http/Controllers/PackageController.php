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
     * Show list of packages (APP Management).
     */
    public function index(Request $request)
    {
        $search = $request->string('q')->trim();

        $packages = \App\Models\Package::query()
            ->with('method')
            ->whereDoesntHave('requisitions')              // ← hides packages that already have a requisition
            ->when($search, fn($q)=> $q->where(function($qq) use($search){
                $qq->where('package_no','like',"%$search%")
                ->orWhere('description','like',"%$search%")
                ->orWhere('package_id','like',"%$search%");
            }))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('packages.index', compact('packages','search'));
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

        $methods = ProcurementMethod::orderBy('name')->get();

        return view('packages.create', compact('methods', 'generatedId'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'package_id'           => 'required|size:6|unique:packages,package_id',
        'package_no'           => 'required|string|max:50|unique:packages,package_no',
        'description'          => 'nullable|string|max:1000',
        'procurement_method_id'=> 'nullable|exists:procurement_methods,id',
        'estimated_cost_bdt'   => 'nullable|numeric|min:0',
    ]);

    \App\Models\Package::create($validated);

    return redirect()
        ->route('packages.index')
        ->with('success', 'Package created successfully.');
}


    public function edit(Package $package)
    {
        $methods = \App\Models\ProcurementMethod::orderBy('name')->get();
        return view('packages.edit', compact('package', 'methods'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'package_no'            => 'required|string|max:50|unique:packages,package_no,' . $package->id,
            'description'           => 'nullable|string|max:1000',
            'procurement_method_id' => 'nullable|exists:procurement_methods,id',
            'estimated_cost_bdt'    => 'nullable|numeric|min:0',
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

    public function all()
    {
        $packages = Package::query()
            ->select([
                 'packages.package_id',
                'packages.package_no',
                'packages.description',
                'procurement_methods.name as procurement_method_name',
                'packages.estimated_cost_bdt'
            ])
            ->leftJoin('procurement_methods', 'packages.procurement_method_id', '=', 'procurement_methods.id')
            ->orderBy('packages.id', 'ASC')
            ->get();

        return view('packages.all', compact('packages'));
    }
    
     public function downloadExcel()
    {
        return Excel::download(new PackagesExport, 'packages.xlsx', ExcelFormat::XLSX);
    }

        public function sampleTemplate()
    {
        return Excel::download(new PackagesSampleExport, 'packages_sample.xlsx');
    }

}
