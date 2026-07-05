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

        // ---------------------------------------------
        // Fiscal year filter (packages.fiscal_year; requisitions via package)
        // ---------------------------------------------
        $fiscalYear = $request->input('fiscal_year');
        if ($fiscalYear && !in_array($fiscalYear, PackageController::fiscalYearOptions(), true)) {
            $fiscalYear = null;
        }
        $fyPackageIds = $fiscalYear
            ? Package::where('fiscal_year', $fiscalYear)->pluck('id')->all()
            : [];

        $withFiscalYear = function ($query) use ($fiscalYear, $fyPackageIds) {
            return $fiscalYear ? $query->whereIn('package_id', $fyPackageIds) : $query;
        };

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
        $packagesTotal = $withPeriod(
            Package::query()->when($fiscalYear, fn ($q) => $q->where('fiscal_year', $fiscalYear))
        )->count();

        $requisitionsTotal = $withPeriod($withFiscalYear(Requisition::query()))->count();

        $packagesWithoutReqTotal = Package::whereDoesntHave('requisitions')
            ->when($fiscalYear, fn ($q) => $q->where('fiscal_year', $fiscalYear))
            ->count();

        // ---------------------------------------------
        // Named status cards
        // ---------------------------------------------
        $statusIds = RequisitionStatus::pluck('id', 'name');

        $countByStatus = function (string $name) use ($statusIds, $withPeriod, $withFiscalYear) {
            $id = $statusIds[$name] ?? null;
            return $id
                ? $withPeriod($withFiscalYear(Requisition::where('requisition_status_id', $id)))->count()
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
            ->leftJoin('requisitions as r', function ($join) use ($dateColumn, $startDate, $endDate, $fiscalYear, $fyPackageIds) {
                $join->on('r.requisition_status_id', '=', 'requisition_statuses.id');
                if ($startDate && $endDate) {
                    $join->whereBetween("r.$dateColumn", [$startDate, $endDate]);
                } elseif ($startDate) {
                    $join->where("r.$dateColumn", '>=', $startDate);
                } elseif ($endDate) {
                    $join->where("r.$dateColumn", '<=', $endDate);
                }
                if ($fiscalYear) {
                    $join->whereIn('r.package_id', $fyPackageIds);
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
            ->leftJoin('requisitions as r', function ($join) use ($dateColumn, $startDate, $endDate, $fiscalYear, $fyPackageIds) {
                $join->on('r.department_id', '=', 'departments.id');
                if ($startDate && $endDate) {
                    $join->whereBetween("r.$dateColumn", [$startDate, $endDate]);
                } elseif ($startDate) {
                    $join->where("r.$dateColumn", '>=', $startDate);
                } elseif ($endDate) {
                    $join->where("r.$dateColumn", '<=', $endDate);
                }
                if ($fiscalYear) {
                    $join->whereIn('r.package_id', $fyPackageIds);
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
            ->leftJoin('requisitions as r', function ($join) use ($dateColumn, $startDate, $endDate, $fiscalYear, $fyPackageIds) {
                $join->on('r.procurement_type_id', '=', 'procurement_types.id');
                if ($startDate && $endDate) {
                    $join->whereBetween("r.$dateColumn", [$startDate, $endDate]);
                } elseif ($startDate) {
                    $join->where("r.$dateColumn", '>=', $startDate);
                } elseif ($endDate) {
                    $join->where("r.$dateColumn", '<=', $endDate);
                }
                if ($fiscalYear) {
                    $join->whereIn('r.package_id', $fyPackageIds);
                }
            })
            ->groupBy('procurement_types.id', 'procurement_types.name')
            ->orderBy('procurement_types.name')
            ->get();

        //Requistion status wise

         $statusIds = RequisitionStatus::whereIn('name', [
        'Initiate',
        'Tender Opened',
        'Evaluation Completed',
        'Contract Signed',
        'Delivered',
             ])->pluck('id', 'name')->toArray();

        // ---------------------------------------------
        // Assigned Person (officer) wise requisition status
        // with percentage summary
        // ---------------------------------------------
        $allStatuses = RequisitionStatus::orderBy('id')->get(['id', 'name', 'color']);

        // Received APP = requisitions where this officer was selected as the
        // assigned officer on the Add Requisition form (requisitions.officer_name).
        $personStatusRows = $withPeriod($withFiscalYear(Requisition::query()))
            ->select('officer_name', 'requisition_status_id', DB::raw('COUNT(*) AS total'))
            ->groupBy('officer_name', 'requisition_status_id')
            ->get();

        // Assigned APP (packages) per officer — respects the same FY/date filters
        $officerNamesById = \App\Models\Officer::pluck('name', 'id');
        $assignedAppByPerson = [];
        $assignedAppRows = $withPeriod(
                Package::query()->when($fiscalYear, fn ($q) => $q->where('fiscal_year', $fiscalYear))
            )
            ->whereNotNull('assigned_officer_id')
            ->select('assigned_officer_id', DB::raw('COUNT(*) AS total'))
            ->groupBy('assigned_officer_id')
            ->get();
        foreach ($assignedAppRows as $row) {
            $name = trim((string) ($officerNamesById[$row->assigned_officer_id] ?? ''));
            if ($name !== '') {
                $assignedAppByPerson[$name] = (int) $row->total;
            }
        }

        $assignedPersonStats = [];
        foreach ($personStatusRows as $r) {
            $person = trim((string) $r->officer_name);
            if ($person === '') {
                $person = 'Unassigned';
            }

            if (!isset($assignedPersonStats[$person])) {
                $assignedPersonStats[$person] = [
                    'person' => $person,
                    'counts' => [], // status_id => count
                    'total'  => 0,
                ];
            }

            $count = (int) $r->total;
            $assignedPersonStats[$person]['total'] += $count;
            if (!is_null($r->requisition_status_id)) {
                $sid = (int) $r->requisition_status_id;
                $assignedPersonStats[$person]['counts'][$sid] =
                    ($assignedPersonStats[$person]['counts'][$sid] ?? 0) + $count;
            }
        }

        // Officers with assigned APPs but no requisitions yet still get a row
        foreach ($assignedAppByPerson as $person => $assigned) {
            if (!isset($assignedPersonStats[$person])) {
                $assignedPersonStats[$person] = [
                    'person' => $person,
                    'counts' => [],
                    'total'  => 0,
                ];
            }
        }

        // Assigned APP = 100% base; Received & Pending (and each status) are % of it.
        // People with requisitions but no assigned APP fall back to their received total.
        $officerIdsByName = $officerNamesById->flip();
        foreach ($assignedPersonStats as &$ps) {
            $assigned = $assignedAppByPerson[$ps['person']] ?? 0;
            $received = $ps['total'];
            $base     = $assigned > 0 ? $assigned : $received;

            $ps['officer_id']   = $officerIdsByName[$ps['person']] ?? null;
            $ps['assigned_app'] = $assigned;
            $ps['received_app'] = $received;
            $ps['pending']      = max(0, $assigned - $received);
            $ps['pct_base']     = $base;
            $ps['received_pct'] = $base > 0 ? round($received / $base * 100, 1) : 0;
            $ps['pending_pct']  = $base > 0 ? round($ps['pending'] / $base * 100, 1) : 0;
        }
        unset($ps);

        // Progress weight per status (Initiate 20% ... Delivered 100%)
        $statusProgressByName = [
            'Initiate'             => 20,
            'Tender Opened'        => 40,
            'Evaluation Completed' => 60,
            'Contract Signed'      => 80,
            'Delivered'            => 100,
        ];
        $statusProgressById = [];
        foreach ($allStatuses as $st) {
            $statusProgressById[$st->id] = $statusProgressByName[$st->name] ?? 0;
        }

        // Compute each person's weighted progress % (avg across their requisitions)
        foreach ($assignedPersonStats as &$ps) {
            $weighted = 0;
            foreach ($ps['counts'] as $sid => $cnt) {
                $weighted += $cnt * ($statusProgressById[$sid] ?? 0);
            }
            $ps['progress_pct'] = $ps['total'] > 0 ? round($weighted / $ps['total'], 1) : 0;
        }
        unset($ps);

        // Sort by who has the most: Initiate, then Tender Opened,
        // then Evaluation Completed, then Contract Signed (desc), then total
        $orderStatusIds = [];
        foreach (['Initiate', 'Tender Opened', 'Evaluation Completed', 'Contract Signed'] as $name) {
            if (isset($statusIds[$name])) {
                $orderStatusIds[] = (int) $statusIds[$name];
            }
        }
        uasort($assignedPersonStats, function ($a, $b) use ($orderStatusIds) {
            $av = [];
            $bv = [];
            foreach ($orderStatusIds as $sid) {
                $av[] = $a['counts'][$sid] ?? 0;
                $bv[] = $b['counts'][$sid] ?? 0;
            }
            $av[] = $a['total'];
            $bv[] = $b['total'];
            return $bv <=> $av;
        });
        $assignedPersonStats = array_values($assignedPersonStats);

        //Requistion status wise

         $statusIds = RequisitionStatus::whereIn('name', [
        'Initiate',
        'Tender Opened',
        'Evaluation Completed',
        'Contract Signed',
        'Delivered',
             ])->pluck('id', 'name')->toArray();

        // ---------------------------------------------
        // Return to view
        // $start / $end are strings for the form and label
        // ---------------------------------------------
        return view('dashboard', compact(   
            
             'statusIds',

            // filters (strings for inputs/labels)
            'start', 'end', 'fiscalYear',

            // KPIs
            'packagesTotal', 'requisitionsTotal', 'packagesWithoutReqTotal',

            // status cards
            'initiateCount', 'evaluationCount', 'contractSignedCount', 'deliveredCount', 'tenderOpenedCount',

            // tables
            'statusCounts', 'departmentCounts', 'typeCounts',

            // assigned person wise status breakdown
            'allStatuses', 'assignedPersonStats',

        ));
    }
}
