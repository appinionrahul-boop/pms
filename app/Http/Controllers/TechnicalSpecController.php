<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Requisition;
use App\Models\TechnicalSpec;
use Illuminate\Http\Request;

class TechnicalSpecController extends Controller
{
    // Index: Package list with first requisition created date (if any), filter by package id/no
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $packages = Package::query()
            ->with(['requisitions' => function($x){ $x->select('id','package_id','created_at')->orderBy('created_at'); }])
            ->when($q !== '', function($x) use ($q){
                $x->where(function($y) use ($q){
                    $y->where('package_id','like',"%{$q}%")
                      ->orWhere('package_no','like',"%{$q}%");
                });
            })
            ->whereHas('technicalSpecs') // only packages that already have specs; remove if you want to list all
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('technical_specs.index', compact('packages','q'));
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
            'package_id'      => ['required','exists:packages,id'],
            'spec_name'       => ['required','string','max:255'],
            'quantity'        => ['nullable','numeric','min:0'],
            'unit_price_bdt'  => ['nullable','numeric','min:0'],
            'total_price_bdt' => ['nullable','numeric','min:0'],
        ]);

        // If total not given, compute qty*unit
        if (empty($validated['total_price_bdt']) && !empty($validated['quantity']) && !empty($validated['unit_price_bdt'])) {
            $validated['total_price_bdt'] = (float)$validated['quantity'] * (float)$validated['unit_price_bdt'];
        }

        TechnicalSpec::create($validated);

        return redirect()
            ->route('techspecs.show', $validated['package_id'])
            ->with('success', 'Technical specification added.');
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
        ]);

        if (empty($validated['total_price_bdt']) && !empty($validated['quantity']) && !empty($validated['unit_price_bdt'])) {
            $validated['total_price_bdt'] = (float)$validated['quantity'] * (float)$validated['unit_price_bdt'];
        }

        $spec->update($validated);

        return redirect()
            ->route('techspecs.show', $validated['package_id'])
            ->with('success', 'Technical specification updated.');
    }

    public function destroy(TechnicalSpec $spec)
    {
        $packageId = $spec->package_id;
        $spec->delete();

        return redirect()
            ->route('techspecs.show', $packageId)
            ->with('success', 'Technical specification deleted.');
    }
}
