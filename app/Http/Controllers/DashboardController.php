<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionStatus;
use App\Models\Package;

class DashboardController extends Controller
{
     public function index()
    {
        // Map status names -> IDs once
        $statusIds = RequisitionStatus::pluck('id', 'name'); // ['Initiate'=>1, 'Evaluation Completed'=>2, ...]

        // Status buckets
        $initiateCount   = Requisition::where('requisition_status_id', $statusIds['Initiate'] ?? 0)->count();
        $evaluationCount = Requisition::where('requisition_status_id', $statusIds['Evaluation Completed'] ?? 0)->count();
        $contractCount   = Requisition::where('requisition_status_id', $statusIds['Contract Signed'] ?? 0)->count();
        $deliveredCount  = Requisition::where('requisition_status_id', $statusIds['Delivered'] ?? 0)->count();

        // App KPIs
        $packagesTotal            = Package::count();                                // total App Management created
        $requisitionsTotal        = Requisition::count();                            // total Requisition created
        $packagesWithoutReqTotal  = Package::doesntHave('requisitions')->count();    // Apps with no requisition yet

        return view('dashboard', compact(
            'initiateCount',
            'evaluationCount',
            'contractCount',
            'deliveredCount',
            'packagesTotal',
            'requisitionsTotal',
            'packagesWithoutReqTotal',
        ));
    }
}
