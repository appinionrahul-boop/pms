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
        // Filters: start & end (YYYY-MM-DD from the view)
        // ---------------------------------------------
        // Raw strings for the view <input type="date"> values
        $start = $request->input('start'); // e.g., 2025-01-01 or null
        $end   = $request->input('end');   // e.g., 2025-03-31 or null

        // Parse into Carbon for querying (inclusive by day)
        $startDate = null;
        $endDate   = null;

        if (!empty($start)) {
            try { $startDate = Carbon::parse($start)->startOfDay(); } catch (\Throwable $e) {}
        }
        if (!empty($end)) {
            try { $endDate = Carbon::parse($end)->endOfDay(); } catch (\Throwable $e) {}
        }

        // If both set but out of order, swap
        if ($startDate && $endDate && $endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            // Also swap the raw strings so the form reflects the swap
            [$start, $end] = [optional($startDate)->toDateString(), optional($endDate)->toDateString()];
        }

        // Choose the date column to filter by (adjust if needed)
        $dateColumn = 'created_at';

        // Helper: apply optional date filter (handles 3 cases)
        $withPeriod = function ($query) use ($dateColumn, $startDate, $endDate) {
            if ($startDate && $endDate) {
                return $query->whereBetween($dateColumn, [$startDate, $endDate]);
            } elseif ($startDate) {
                return $query->where($dateColumn, '>=', $startDate);
            } elseif ($endDate) {
                return $query->where($dateColumn, '<=', $endDate);
            }
            return $query; // no filter
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

        // Tender Opened (case variants safe-guard)
        $tenderOpenedName = collect(['Tender Opened', 'tender opened', 'Tender opened'])
            ->first(fn ($n) => isset($statusIds[$n])) ?? 'Tender Opened';
        $tenderOpenedCount = $countByStatus($tenderOpenedName);

        // ---------------------------------------------
        // Status-wise table (include zeros via LEFT JOIN)
        // ---------------------------------------------
        $statusCounts = RequisitionStatus::query()
            ->select('requisition_statuses.id', 'requisition_statuses.name', DB::raw('COUNT(r.id) AS total'))
            ->leftJoin('requisitions as r', function ($join) use ($dateColumn, $startDate, $endDate) {
                $join->on('r.requisition_status_id', '=', 'requisition_statuses.id');
                if ($startDate && $endDate) {
                    $join->whereBetween("r.$dateColumn", [$startDate, $endDate]);
                } elseif ($startDate) {
                    $join->where("r.$dateColumn", '>=', $startDate);
                } elseif ($endDate) {
                    $join->where("r.$dateColumn", '<=', $endDate);
                }
            })
            ->groupBy('requisition_statuses.id', 'requisition_statuses.name')
            ->orderBy('requisition_statuses.id')
            ->get();

        // ---------------------------------------------
        // Department-wise table (include zeros via LEFT JOIN)
        // ---------------------------------------------
        $departmentCounts = Department::query()
            ->select('departments.id', 'departments.name', DB::raw('COUNT(r.id) AS total'))
            ->leftJoin('requisitions as r', function ($join) use ($dateColumn, $startDate, $endDate) {
                $join->on('r.department_id', '=', 'departments.id');
                if ($startDate && $endDate) {
                    $join->whereBetween("r.$dateColumn", [$startDate, $endDate]);
                } elseif ($startDate) {
                    $join->where("r.$dateColumn", '>=', $startDate);
                } elseif ($endDate) {
                    $join->where("r.$dateColumn", '<=', $endDate);
                }
            })
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('departments.name')
            ->get();

        // ---------------------------------------------
        // Procurement Type-wise table (include zeros via LEFT JOIN)
        // ---------------------------------------------
        $typeCounts = ProcurementType::query()
            ->select('procurement_types.id', 'procurement_types.name', DB::raw('COUNT(r.id) AS total'))
            ->leftJoin('requisitions as r', function ($join) use ($dateColumn, $startDate, $endDate) {
                $join->on('r.procurement_type_id', '=', 'procurement_types.id');
                if ($startDate && $endDate) {
                    $join->whereBetween("r.$dateColumn", [$startDate, $endDate]);
                } elseif ($startDate) {
                    $join->where("r.$dateColumn", '>=', $startDate);
                } elseif ($endDate) {
                    $join->where("r.$dateColumn", '<=', $endDate);
                }
            })
            ->groupBy('procurement_types.id', 'procurement_types.name')
            ->orderBy('procurement_types.name')
            ->get();

        // ---------------------------------------------
        // Return to view
        // $start / $end are strings for the form and label
        // ---------------------------------------------
        return view('dashboard', compact(
            // filters (strings for inputs/labels)
            'start', 'end',

            // KPIs
            'packagesTotal', 'requisitionsTotal', 'packagesWithoutReqTotal',

            // status cards
            'initiateCount', 'evaluationCount', 'contractSignedCount', 'deliveredCount', 'tenderOpenedCount',

            // tables
            'statusCounts', 'departmentCounts', 'typeCounts'
        ));
    }
}
