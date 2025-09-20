<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Requisition;
use App\Models\RequisitionStatus;
use App\Models\ProcurementType;
use App\Models\Package;
use App\Models\Department;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ---------------------------------------------
        // Filters
        // ---------------------------------------------
        $allowedYears = [2024, 2025, 2026, 2027, 2028];

        $monthInput = $request->input('month'); // 1..12 or empty
        $yearInput  = $request->input('year');  // 2025..2028 or empty

        $usePeriod = false;
        $start = null;
        $end   = null;

        if ($yearInput && in_array((int)$yearInput, $allowedYears)) {
            $year = (int)$yearInput;

            if (!empty($monthInput)) {
                // Year + Month -> that month of the year
                $month = max(1, min(12, (int)$monthInput));
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end   = Carbon::create($year, $month, 1)->endOfMonth();
            } else {
                // Year only -> full year
                $start = Carbon::create($year, 1, 1)->startOfYear();
                $end   = Carbon::create($year, 12, 31)->endOfYear();
            }
            $usePeriod = true;
        }

        // Choose the date column to filter by (change if you have a business date)
        $dateColumn = 'created_at';

        // Helper: apply optional date filter
        $withPeriod = function ($query) use ($usePeriod, $dateColumn, $start, $end) {
            return $usePeriod ? $query->whereBetween($dateColumn, [$start, $end]) : $query;
        };

        // ---------------------------------------------
        // KPIs
        // ---------------------------------------------
        $packagesTotal = $withPeriod(Package::query())->count();
        $requisitionsTotal = $withPeriod(Requisition::query())->count();
        $packagesWithoutReqTotal = $withPeriod(Package::query())
            ->doesntHave('requisitions')
            ->count();

        // ---------------------------------------------
        // Named status cards
        // ---------------------------------------------
        $statusIds = RequisitionStatus::pluck('id', 'name');

        $countByStatus = function (string $name) use ($statusIds, $withPeriod) {
            $id = $statusIds[$name] ?? null;
            return $id
                ? $withPeriod(Requisition::where('requisition_status_id', $id))->count()
                : 0;
        };

        $initiateCount       = $countByStatus('Initiate');
        $evaluationCount     = $countByStatus('Evaluation Completed');
        $contractSignedCount = $countByStatus('Contract Signed');
        $deliveredCount      = $countByStatus('Delivered');

        // Tender Opened (handle common casing variants)
        $tenderOpenedName = collect(['Tender Opened', 'tender opened', 'Tender opened'])
            ->first(fn ($n) => isset($statusIds[$n])) ?? 'Tender Opened';
        $tenderOpenedCount = $countByStatus($tenderOpenedName);

        // ---------------------------------------------
        // Status-wise table (include 0 via LEFT JOIN)
        // ---------------------------------------------
        $statusCounts = RequisitionStatus::query()
            ->select('requisition_statuses.id', 'requisition_statuses.name', DB::raw('COUNT(r.id) AS total'))
            ->leftJoin('requisitions as r', function ($join) use ($usePeriod, $dateColumn, $start, $end) {
                $join->on('r.requisition_status_id', '=', 'requisition_statuses.id');
                if ($usePeriod) {
                    $join->whereBetween("r.$dateColumn", [$start, $end]);
                }
            })
            ->groupBy('requisition_statuses.id', 'requisition_statuses.name')
            ->orderBy('requisition_statuses.id')
            ->get();

        // ---------------------------------------------
        // Department-wise table (include 0 via LEFT JOIN)
        // ---------------------------------------------
        $departmentCounts = Department::query()
            ->select('departments.id', 'departments.name', DB::raw('COUNT(r.id) AS total'))
            ->leftJoin('requisitions as r', function ($join) use ($usePeriod, $dateColumn, $start, $end) {
                $join->on('r.department_id', '=', 'departments.id');
                if ($usePeriod) {
                    $join->whereBetween("r.$dateColumn", [$start, $end]);
                }
            })
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('departments.name')
            ->get();

        // ---------------------------------------------
        // Procurement Type-wise table (include 0 via LEFT JOIN)
        // ---------------------------------------------
        $typeCounts = ProcurementType::query()
            ->select('procurement_types.id', 'procurement_types.name', DB::raw('COUNT(r.id) AS total'))
            ->leftJoin('requisitions as r', function ($join) use ($usePeriod, $dateColumn, $start, $end) {
                $join->on('r.procurement_type_id', '=', 'procurement_types.id');
                if ($usePeriod) {
                    $join->whereBetween("r.$dateColumn", [$start, $end]);
                }
            })
            ->groupBy('procurement_types.id', 'procurement_types.name')
            ->orderBy('procurement_types.name')
            ->get();

        // ---------------------------------------------
        // Labels for the view
        // ---------------------------------------------
        $month = $usePeriod && !empty($monthInput) ? (int)$monthInput : null; // null means "All months"
        $year  = $usePeriod ? (int)$yearInput : null;                          // null means "All time"
        $monthName = $month ? Carbon::create(null, $month, 1)->format('F') : 'All Months';

        return view('dashboard', compact(
            // filters/labels
            'month', 'year', 'monthName', 'allowedYears',

            // KPIs
            'packagesTotal', 'requisitionsTotal', 'packagesWithoutReqTotal',

            // status cards
            'initiateCount', 'evaluationCount', 'contractSignedCount', 'deliveredCount', 'tenderOpenedCount',

            // tables
            'statusCounts', 'departmentCounts', 'typeCounts'
        ));
    }
}
