<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Requisition;
use App\Exports\ProcurementReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $req)
    {
        // Filter dropdown data
        $departments      = \DB::table('departments')->orderBy('name')->pluck('name', 'id');
        $methods          = \DB::table('procurement_methods')->orderBy('name')->pluck('name', 'id');
        $procurementTypes = \DB::table('procurement_types')->orderBy('name')->pluck('name', 'id');
        $reqStatuses      = \DB::table('requisition_statuses')->orderBy('name')->pluck('name', 'id');
        $lcStatuses       = \DB::table('lc_statuses')->orderBy('name')->pluck('name', 'id');

        // Base query with all fields shown in the image
        $q = Requisition::query()
            ->leftJoin('packages', 'packages.id', '=', 'requisitions.package_id')
            ->leftJoin('departments', 'departments.id', '=', 'requisitions.department_id')
            ->leftJoin('procurement_methods as pm', 'pm.id', '=', 'requisitions.procurement_method_id')
            ->leftJoin('procurement_types  as pt', 'pt.id', '=', 'requisitions.procurement_type_id')
            ->leftJoin('requisition_statuses as rs', 'rs.id', '=', 'requisitions.requisition_status_id')
            ->leftJoin('lc_statuses as lc', 'lc.id', '=', 'requisitions.lc_status_id')
            ->leftJoin('users as un', 'un.id', '=', 'requisitions.assigned_officer_id')
            ->leftJoin('units as uu', 'uu.id', '=', 'requisitions.unit_id')
            ->leftJoin('approving_authorities as aa', 'aa.id', '=', 'requisitions.approving_authority_id')
            ->select([
                // Left column in your image
                'packages.package_id                as package_id',
                'packages.package_no                as package_no',
                'requisitions.description           as description',
                'pm.name                            as procurement_method',
                'uu.name                            as unit',
                'requisitions.vendor_name           as vendor_name',
                // If you have a dedicated "type_of_goods" field/table, this maps it.
                // Otherwise it will use the requisitions column if present.
                // 'requisitions.type_of_goods         as type_of_goods',
                'pt.name                            as procurement_type',
                'requisitions.official_estimated_cost_bdt as official_estimated_cost_bdt',
                'requisitions.requisition_receiving_date as requisition_receiving_date',
                'requisitions.delivery_date         as delivery_date',
                'requisitions.reference_link        as reference_link',
                'requisitions.officer_name as officer_name',

                // Right column in your image
                'rs.name                            as requisition_status',
                'requisitions.estimated_cost_bdt    as estimated_cost_bdt',
                'requisitions.quantity              as quantity',
                'departments.name                   as department',
                'un.name                            as assigned_officer',
                'requisitions.tech_spec_file        as tech_spec_file',
                'aa.name                            as approving_authority',
                'requisitions.signing_date          as signing_date',
                'lc.name                            as lc_status',
                'requisitions.reference_annex       as reference_annex',

                // utility
                'requisitions.created_at            as created_at',
                'requisitions.id'
            ]);

        // ---- Filters (unchanged, but aligned to *_id names) ----
        if ($req->filled('requisition_status_id')) {
            $q->where('requisitions.requisition_status_id', $req->requisition_status_id);
        }
        if ($req->filled('procurement_type_id')) {
            $q->where('requisitions.procurement_type_id', $req->procurement_type_id);
        }
        if ($req->filled('procurement_method_id')) {
            $q->where('requisitions.procurement_method_id', $req->procurement_method_id);
        }
        if ($req->filled('lc_status_id')) {
            $q->where('requisitions.lc_status_id', $req->lc_status_id);
        }
        if ($req->filled('department_id')) {
            $q->where('requisitions.department_id', $req->department_id);
        }

        // Keyword search (Package No / Vendor / Description / Package ID)
        if ($req->filled('k')) {
            $k = '%'.$req->k.'%';
            $q->where(function($w) use ($k) {
                $w->where('packages.package_no', 'like', $k)
                  ->orWhere('requisitions.package_no', 'like', $k)
                  ->orWhere('requisitions.vendor_name', 'like', $k)
                  ->orWhere('requisitions.description', 'like', $k)
                  ->orWhere('packages.package_id', 'like', $k);
            });
        }

        // Date range filter (created_at; change if you prefer another date)
        if ($req->filled('date_start')) {
            $q->whereDate('requisitions.created_at', '>=', Carbon::parse($req->date_start));
        }
        if ($req->filled('date_end')) {
            $q->whereDate('requisitions.created_at', '<=', Carbon::parse($req->date_end));
        }

        $records = $q->latest('requisitions.id')
            ->paginate(25)
            ->appends($req->query());

        return view('reports.procurements', compact(
            'records', 'departments', 'procurementTypes', 'methods', 'reqStatuses', 'lcStatuses'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->all();

        return Excel::download(new ProcurementReportExport($filters), 'procurement_report.xlsx');
    }
}
