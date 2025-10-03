<?php

namespace App\Http\Controllers;

use App\Models\{
    Package,
    Requisition,
    ProcurementMethod,
    Unit,
    Department,
    ProcurementType,
    RequisitionStatus,
    ApprovingAuthority,
    LcStatus,
    Officer,
    Notificaton
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TechnicalSpecsImport;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;

class RequisitionController extends Controller
{
    public function create(Request $request, Package $package = null)
    {
        // allow /requisitions/create?package_id=xx OR /packages/{package}/requisitions/create
        if (!$package && $request->filled('package_id')) {
            $package = Package::findOrFail($request->package_id);
        }

        $data = [
            'package'     => $package,
            'methods'     => ProcurementMethod::orderBy('name')->get(),
            'units'       => Unit::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'types'       => ProcurementType::orderBy('name')->get(),
            'statuses'    => RequisitionStatus::orderBy('name')->get(),
            'authorities' => ApprovingAuthority::orderBy('name')->get(),
            'lcStatuses'  => LcStatus::orderBy('name')->get(),
            // NEW: officers for dropdown
            'officers'    => Officer::orderBy('name')->get(),
        ];

        return view('requisitions.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id'                  => ['required','exists:packages,id'],
            'package_no'                  => ['required','string','max:50'],
            'description'                 => ['nullable','string','max:2000'],
            'procurement_method_id'       => ['nullable','exists:procurement_methods,id'],
            'unit_id'                     => ['nullable','exists:units,id'],
            'quantity'                    => ['nullable','numeric','min:0'],
            'vendor_name'                 => ['nullable','string','max:255'],
            'procurement_type_id'         => ['nullable','exists:procurement_types,id'],
            'official_estimated_cost_bdt' => ['nullable','numeric','min:0'],
            'estimated_cost_bdt'          => ['nullable','numeric','min:0'],
            'requisition_receiving_date'  => ['nullable','date'],
            'delivery_date'               => ['nullable','date'],
            'department_id'               => ['nullable','exists:departments,id'],
            'requisition_status_id'       => ['nullable','exists:requisition_statuses,id'],
            'approving_authority_id'      => ['nullable','exists:approving_authorities,id'],
            'signing_date'                => ['nullable','date'],
            'lc_status_id'                => ['nullable','exists:lc_statuses,id'],
            'reference_link'              => ['nullable','url'],
            'reference_annex'             => ['nullable','file','max:5120'],
            'tech_spec'                   => ['nullable','file','mimes:xlsx,xls,csv','max:10240'],
            'comments'                    => ['nullable','string','max:5000'],
            // NEW: officer must exist by name in officers table
            'officer_name'                => ['nullable','string','max:255','exists:officers,name'],
        ]);

        // files
        $annexPath = $request->file('reference_annex')?->store('annex','public');
        $techPath  = $request->file('tech_spec')?->store('techspecs','public');

        // create requisition
        $req = Requisition::create([
            'package_id'                  => $request->package_id,
            'package_no'                  => $request->package_no,
            'description'                 => $request->description,
            'procurement_method_id'       => $request->procurement_method_id,
            'unit_id'                     => $request->unit_id,
            'quantity'                    => $request->quantity,
            'vendor_name'                 => $request->vendor_name,
            'procurement_type_id'         => $request->procurement_type_id,
            'official_estimated_cost_bdt' => $request->official_estimated_cost_bdt,
            'estimated_cost_bdt'          => $request->estimated_cost_bdt,
            'requisition_receiving_date'  => $request->requisition_receiving_date,
            'delivery_date'               => $request->delivery_date,
            'department_id'               => $request->department_id,
            'requisition_status_id'       => $request->requisition_status_id,
            'approving_authority_id'      => $request->approving_authority_id,
            'signing_date'                => $request->signing_date,
            'lc_status_id'                => $request->lc_status_id,
            'reference_link'              => $request->reference_link,
            'reference_annex'             => $annexPath,
            'tech_spec_file'              => $techPath,
            'comments'                    => $request->comments,
            // NEW
            'officer_name'                => $request->officer_name,
        ]);

        // Import tech spec rows into table, if provided
        if ($techPath) {
            Excel::import(new TechnicalSpecsImport($req->package_id), storage_path("app/public/{$techPath}"));
        }

         // --- NEW: Notification on create ---
        $userName = optional(auth()->user())->name ?? 'System';
        Notificaton::create([
            'text'    => "Package No {$req->package_no} requisition has been created by {$userName}.",
            'is_seen' => false,
        ]);

        return redirect()->route('packages.index')
            ->with('success', 'Requisition submitted. Package removed from the list.');
    }

    public function data(Request $request)
    {
        $q = Requisition::query()
            ->with(['package','status','procurementType','method','lcStatus'])
            ->select('requisitions.*');

        // Removed dd($q); so DataTables can return data

        return DataTables::eloquent($q)
            ->addColumn('pkg', function ($r) {
                $pid = $r->package->package_id ?? '—';
                $pno = $r->package_no ?: '—';
                return e($pid).'<br><small class="text-secondary">'.e($pno).'</small>';
            })
            ->addColumn('status', fn($r) => $r->status->name ?? '—')
            ->addColumn('type',   fn($r) => $r->procurementType->name ?? '—')
            ->addColumn('method', fn($r) => $r->method->name ?? '—')
            ->addColumn('lc',     fn($r) => $r->lcStatus->name ?? '—')
            ->addColumn('cost',   fn($r) => number_format((float)($r->estimated_cost_bdt ?? 0), 2))
            ->addColumn('created',fn($r) => optional($r->created_at)->format('Y-m-d') ?? '')
            ->addColumn('action', function ($r) {
                $view = route('requisitions.show', $r);
                $edit = route('requisitions.edit', $r);
                $del  = route('requisitions.destroy', $r);
                return view('requisitions._actions', compact('view','edit','del','r'))->render();
            })
            ->rawColumns(['pkg','action'])
            ->toJson();
    }

    public function show(\App\Models\Requisition $requisition)
    {
        $requisition->load(['package','status','procurementType','method','lcStatus','department','approvingAuthority']);

        $specs = \App\Models\TechnicalSpec::where('package_id', $requisition->package_id)->get();

        return view('requisitions.show', compact('requisition','specs'));
    }

    public function edit(\App\Models\Requisition $requisition)
    {
        $requisition->load(['package','status','procurementType','method','lcStatus','department','approvingAuthority','unit']);

        $data = [
            'requisition'  => $requisition,
            'methods'      => ProcurementMethod::orderBy('name')->get(),
            'units'        => Unit::orderBy('name')->get(),
            'departments'  => Department::orderBy('name')->get(),
            'types'        => ProcurementType::orderBy('name')->get(),
            'statuses'     => RequisitionStatus::orderBy('name')->get(),
            'authorities'  => ApprovingAuthority::orderBy('name')->get(),
            'lcStatuses'   => LcStatus::orderBy('name')->get(),
            // NEW: officers for dropdown
            'officers'     => Officer::orderBy('name')->get(),
        ];

        return view('requisitions.edit', $data);
    }

    public function update(Request $request, \App\Models\Requisition $requisition)
    {
        $request->validate([
            'description'                 => ['nullable','string','max:2000'],
            'procurement_method_id'       => ['nullable','exists:procurement_methods,id'],
            'unit_id'                     => ['nullable','exists:units,id'],
            'quantity'                    => ['nullable','numeric','min:0'],
            'vendor_name'                 => ['nullable','string','max:255'],
            'procurement_type_id'         => ['nullable','exists:procurement_types,id'],
            'official_estimated_cost_bdt' => ['nullable','numeric','min:0'],
            'estimated_cost_bdt'          => ['nullable','numeric','min:0'],
            'requisition_receiving_date'  => ['nullable','date'],
            'delivery_date'               => ['nullable','date'],
            'department_id'               => ['nullable','exists:departments,id'],
            'requisition_status_id'       => ['nullable','exists:requisition_statuses,id'],
            'approving_authority_id'      => ['nullable','exists:approving_authorities,id'],
            'signing_date'                => ['nullable','date'],
            'lc_status_id'                => ['nullable','exists:lc_statuses,id'],
            'reference_link'              => ['nullable','url'],
            'reference_annex'             => ['nullable','file','max:5120'],
            'tech_spec'                   => ['nullable','file','mimes:xlsx,xls,csv','max:10240'],
            'comments'                    => ['nullable','string','max:5000'],
            // NEW: officer must exist by name in officers table
            'officer_name'                => ['nullable','string','max:255','exists:officers,name'],
        ]);

        // files (replace only if new uploaded)
        if ($request->hasFile('reference_annex')) {
            $requisition->reference_annex = $request->file('reference_annex')->store('annex','public');
        }
        if ($request->hasFile('tech_spec')) {
            $requisition->tech_spec_file = $request->file('tech_spec')->store('techspecs','public');
            // TODO: optionally re-import specs here
        }

        $requisition->fill($request->except(['reference_annex','tech_spec']));
        $requisition->save();

         // --- NEW: Notification on update ---
        $userName = optional(auth()->user())->name ?? 'System';
        Notificaton::create([
            'text'    => "Package No {$requisition->package_no} requisition has been updated by {$userName}.",
            'is_seen' => false,
        ]);

        return redirect()->route('requisitions.show', $requisition)
            ->with('success','Requisition updated successfully.');
    }

    // public function index(Request $request)
    // {
    //     $filters = $request->only([
    //         'k',                 
    //         'status_id',
    //         'procurement_type_id',
    //         'procurement_method_id',
    //         'lc_status_id',
    //         'date_from',
    //         'date_to',
    //     ]);

    //     $q = Requisition::query()
    //         ->with(['package','status','procurementType','method','lcStatus']);

      
    //     if (!empty($filters['k'])) {
    //         $k = '%'.$filters['k'].'%';
    //         $q->where(function($x) use ($k){
    //             $x->where('package_no', 'like', $k)
    //               ->orWhere('vendor_name', 'like', $k)
    //               ->orWhere('description', 'like', $k);
    //         });
    //     }

   
    //     if (!empty($filters['status_id']))              $q->where('requisition_status_id',   $filters['status_id']);
    //     if (!empty($filters['procurement_type_id']))    $q->where('procurement_type_id',     $filters['procurement_type_id']);
    //     if (!empty($filters['procurement_method_id']))  $q->where('procurement_method_id',   $filters['procurement_method_id']);
    //     if (!empty($filters['lc_status_id']))           $q->where('lc_status_id',            $filters['lc_status_id']);

  
    //     if (!empty($filters['date_from'])) $q->whereDate('created_at', '>=', $filters['date_from']);
    //     if (!empty($filters['date_to']))   $q->whereDate('created_at', '<=', $filters['date_to']);

    //     $requisitions = $q->orderByDesc('created_at')
    //         ->paginate(100)
    //         ->withQueryString(); 

    //     return view('requisitions.index', [
    //         'requisitions' => $requisitions,
    //         'filters'      => $filters,
    //         'statuses'     => RequisitionStatus::orderBy('name')->get(),
    //         'types'        => ProcurementType::orderBy('name')->get(),
    //         'methods'      => ProcurementMethod::orderBy('name')->get(),
    //         'lcStatuses'   => LcStatus::orderBy('name')->get(),
    //     ]);
    // }
    public function index(Request $request)
{
    $filters = $request->only([
        'k','status_id','procurement_type_id','procurement_method_id','lc_status_id','date_from','date_to',
    ]);

    // per-page (defaults to 25). Clamp to safe values.
    $perPage = (int) $request->input('per_page', 25);
    $allowed = [10,25,50,100,200];
    if (!in_array($perPage, $allowed, true)) {
        $perPage = 25;
    }

    $q = Requisition::query()
        ->with(['package','status','procurementType','method','lcStatus']);

    if (!empty($filters['k'])) {
        $k = '%'.$filters['k'].'%';
        $q->where(function($x) use ($k){
            $x->where('package_no','like',$k)
              ->orWhere('vendor_name','like',$k)
              ->orWhere('description','like',$k);
        });
    }
    if (!empty($filters['status_id']))             $q->where('requisition_status_id',  $filters['status_id']);
    if (!empty($filters['procurement_type_id']))   $q->where('procurement_type_id',    $filters['procurement_type_id']);
    if (!empty($filters['procurement_method_id'])) $q->where('procurement_method_id',  $filters['procurement_method_id']);
    if (!empty($filters['lc_status_id']))          $q->where('lc_status_id',           $filters['lc_status_id']);
    if (!empty($filters['date_from']))             $q->whereDate('created_at','>=',$filters['date_from']);
    if (!empty($filters['date_to']))               $q->whereDate('created_at','<=',$filters['date_to']);

    $requisitions = $q->orderByDesc('created_at', 'DESC')
        ->paginate($perPage)
        ->withQueryString();

    return view('requisitions.index', [
        'requisitions' => $requisitions,
        'filters'      => $filters,
        'statuses'     => RequisitionStatus::orderBy('name')->get(),
        'types'        => ProcurementType::orderBy('name')->get(),
        'methods'      => ProcurementMethod::orderBy('name')->get(),
        'lcStatuses'   => LcStatus::orderBy('name')->get(),
        'perPage'      => $perPage,
        'allowedPerPage' => $allowed,
    ]);
}
   public function destroy(Requisition $requisition)
{
    try {
        // capture before delete
        $pkgNo = $requisition->package_no;

        $requisition->delete();

        // save notification
        $userName = optional(auth()->user())->name ?? 'System';
        \App\Models\Notificaton::create([
            'text'    => "Package No {$pkgNo} requisition has been deleted by {$userName}.",
            'is_seen' => false,
        ]);

        return redirect()
            ->route('requisitions.index')
            ->with('success', 'Requisition deleted successfully.');
    } catch (\Exception $e) {
        return redirect()
            ->route('requisitions.index')
            ->with('error', 'Failed to delete requisition: ' . $e->getMessage());
    }
}

public function annex(\App\Models\Requisition $requisition)
{
    if (!$requisition->reference_annex) {
        abort(404, 'No annex file for this requisition.');
    }

    $path = Storage::disk('public')->path($requisition->reference_annex);

    if (!is_file($path)) {
        abort(404, 'File missing on disk: '.$requisition->reference_annex);
    }

    // View in browser; use ->download($path) if you want a download prompt
    return response()->file($path); // or: return response()->download($path);
}


}
