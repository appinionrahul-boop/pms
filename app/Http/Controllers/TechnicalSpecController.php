<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Requisition;
use App\Models\TechnicalSpec;
use App\Models\Notificaton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicalSpecController extends Controller
{
    // Index: Package list with first requisition created date (if any), filter by package id/no


public function index(Request $request)
{
    $q = trim((string) $request->get('q', ''));

    $specs = DB::table('technical_specs as ts')
    ->join('packages as p', 'ts.package_id', '=', 'p.id')
    ->select([
        'ts.id as spec_id',          // <-- REQUIRED for edit/delete routes
        'p.package_id',
        'p.package_no',
        'p.description',
        'ts.erp_code',
        'ts.spec_name',
        'ts.quantity',
        'ts.unit_price_bdt',
        'ts.total_price_bdt',
    ])
    ->when($q !== '', function ($x) use ($q) {
        $x->where(function ($y) use ($q) {
            $y->where('p.package_id', 'like', "%{$q}%")
              ->orWhere('p.package_no', 'like', "%{$q}%")
              ->orWhere('ts.spec_name', 'like', "%{$q}%")
              ->orWhere('ts.erp_code', 'like', "%{$q}%");
        });
    })
    ->orderByDesc('p.id')
    ->get();

    return view('technical_specs.index', ['specs' => $specs, 'q' => $q]);

}



    // Details: all specs for a given package
    public function show(Package $package)
    {
        $specs = TechnicalSpec::where('package_id', $package->id)->orderBy('id')->paginate(20);
        return view('technical_specs.show', compact('package','specs'));
    }

    // Global create (select package then enter a spec)
    public function create()
    {
        $packages = Package::orderBy('package_no')->get(['id','package_id','package_no','description']);
        return view('technical_specs.create', compact('packages'));
    }

    // Create for a specific package (shortcut)
    public function createForPackage(Package $package)
    {
        return view('technical_specs.create', ['packages' => collect(), 'package' => $package]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'package_id'      => ['required', 'exists:packages,id'],
        'spec_name'       => ['required', 'string', 'max:255'],
        'quantity'        => ['nullable', 'numeric', 'min:0'],
        'unit_price_bdt'  => ['nullable', 'numeric', 'min:0'],
        'total_price_bdt' => ['nullable', 'numeric', 'min:0'],
        'erp_code'        => ['nullable', 'string', 'max:100'], // ✅ fixed
    ]);

    // Normalize numeric fields
    $validated['quantity']        = $validated['quantity'] ?? 0;
    $validated['unit_price_bdt']  = $validated['unit_price_bdt'] ?? 0;
    $validated['total_price_bdt'] = $validated['total_price_bdt'] ?? 0;

    // If total not given, compute qty * unit
    if (empty($request->total_price_bdt) && $validated['quantity'] > 0 && $validated['unit_price_bdt'] > 0) {
        $validated['total_price_bdt'] = (float) $validated['quantity'] * (float) $validated['unit_price_bdt'];
    }

    TechnicalSpec::create($validated);
    $pkg = Package::find($validated['package_id']);
    $userName = Auth::user()->name ?? 'System';

    Notificaton::create([
        'text' => "Package No {$pkg->package_no} technical spec '{$validated['spec_name']}' has been created by {$userName}.",
         'is_seen' => false,
    ]);


    return redirect()
        ->route('techspecs.index')
        ->with('success', 'Technical specification added successfully.');
}


    public function edit(TechnicalSpec $spec)
    {
        // For dropdown if you want to switch package (optional)
        $packages = Package::orderBy('package_no')->get(['id','package_id','package_no']);
        return view('technical_specs.edit', compact('spec','packages'));
    }

    public function update(Request $request, TechnicalSpec $spec)
    {
        $validated = $request->validate([
            'package_id'      => ['required','exists:packages,id'],
            'spec_name'       => ['required','string','max:255'],
            'quantity'        => ['nullable','numeric','min:0'],
            'unit_price_bdt'  => ['nullable','numeric','min:0'],
            'total_price_bdt' => ['nullable','numeric','min:0'],
              'erp_code' => ['nullable','string','min:0'],
        ]);

        if (empty($validated['total_price_bdt']) && !empty($validated['quantity']) && !empty($validated['unit_price_bdt'])) {
            $validated['total_price_bdt'] = (float)$validated['quantity'] * (float)$validated['unit_price_bdt'];
        }

        $spec->update($validated);
        $userName = Auth::user()->name ?? 'System';

        $pkg = Package::find($validated['package_id']);
        Notificaton::create([
            'text' => "Package No {$pkg->package_no} technical spec '{$validated['spec_name']}' has been updated by {$userName}.",
            'is_seen' => false,
        ]);

        return redirect()
            ->route('techspecs.index')
            ->with('success', 'Technical specification updated.');
    }

    public function destroy(TechnicalSpec $spec)
    {
        $pkg  = Package::find($spec->package_id);
        $name = $spec->spec_name;
        $userName = Auth::user()->name ?? 'System';

        $spec->delete();

        Notificaton::create([
            'text' => "Package No {$pkg->package_no} technical spec '{$name}' has been deleted by {$userName}.",
            'is_seen' => false,
        ]);

        return redirect()
            ->route('techspecs.index')
            ->with('success', 'Technical specification deleted.');
    }
}
