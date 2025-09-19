<?php

namespace App\Exports;

use App\Models\Requisition;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProcurementReportExport implements FromView
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        // Reuse your ReportController logic to apply filters
        // $q = Requisition::query()
        //     ->leftJoin('packages', 'packages.id', '=', 'requisitions.package_id')
        //     ->leftJoin('departments', 'departments.id', '=', 'requisitions.department_id')
        //     ->leftJoin('procurement_methods as pm', 'pm.id', '=', 'requisitions.procurement_method_id')
        //     ->leftJoin('procurement_types  as pt', 'pt.id', '=', 'requisitions.procurement_type_id')
        //     ->leftJoin('requisition_statuses as rs', 'rs.id', '=', 'requisitions.requisition_status_id')
        //     ->leftJoin('lc_statuses as lc', 'lc.id', '=', 'requisitions.lc_status_id')
        //     ->select([
        //         'packages.package_id as package_id',
        //         'packages.package_no as package_no',
        //         'requisitions.description as description',
        //         'pm.name as procurement_method',
        //         'rs.name as requisition_status',
        //         'requisitions.vendor_name as vendor_name',
        //         'departments.name as department',
        //         'pt.name as procurement_type',
        //         'lc.name as lc_status',
        //     ]);

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

        // Apply filters just like ReportController (simplified)
        if (!empty($this->filters['department_id'])) {
            $q->where('requisitions.department_id', $this->filters['department_id']);
        }
        if (!empty($this->filters['procurement_type_id'])) {
            $q->where('requisitions.procurement_type_id', $this->filters['procurement_type_id']);
        }
        if (!empty($this->filters['procurement_method_id'])) {
            $q->where('requisitions.procurement_method_id', $this->filters['procurement_method_id']);
        }
        if (!empty($this->filters['requisition_status_id'])) {
            $q->where('requisitions.requisition_status_id', $this->filters['requisition_status_id']);
        }
        if (!empty($this->filters['lc_status_id'])) {
            $q->where('requisitions.lc_status_id', $this->filters['lc_status_id']);
        }

        $records = $q->get();

        return view('reports.export', compact('records'));
    }
}
