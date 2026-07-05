<?php
/**
 * TEST-DATA tool: randomly fill Assigned Officer & Fiscal Year on all packages.
 * For testing only — do not run on real production data you want to keep.
 *
 * View current state:
 *   https://YOUR-DOMAIN/seed-test-data.php?key=44a781d7e9f55847af7654f5f3cf159a
 * Fill every package with a random officer + fiscal year:
 *   https://YOUR-DOMAIN/seed-test-data.php?key=44a781d7e9f55847af7654f5f3cf159a&action=seed
 * Clear both fields on every package (undo):
 *   https://YOUR-DOMAIN/seed-test-data.php?key=44a781d7e9f55847af7654f5f3cf159a&action=clear
 * Remove this file from the server:
 *   https://YOUR-DOMAIN/seed-test-data.php?key=44a781d7e9f55847af7654f5f3cf159a&delete=1
 */

$SECRET_KEY = '44a781d7e9f55847af7654f5f3cf159a';

$root = is_file(__DIR__ . '/../vendor/autoload.php') ? dirname(__DIR__) : __DIR__;

header('Content-Type: text/plain; charset=utf-8');

if (!is_file($root . '/vendor/autoload.php')) {
    http_response_code(500);
    exit("ERROR: vendor/autoload.php not found.\n");
}

if (!isset($_GET['key']) || !hash_equals($SECRET_KEY, (string) $_GET['key'])) {
    http_response_code(403);
    exit('Forbidden: missing or wrong key.');
}

require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (isset($_GET['delete'])) {
    @unlink(__FILE__);
    exit(is_file(__FILE__) ? "Could not delete — remove manually via File Manager.\n"
                           : "This script has deleted itself.\n");
}

use Illuminate\Support\Facades\DB;

function showState(): void
{
    echo "== Current state ==\n";
    echo "Database: " . DB::connection()->getDatabaseName() . "\n";
    echo "Packages total: " . DB::table('packages')->count() . "\n";
    echo "  with assigned officer: " . DB::table('packages')->whereNotNull('assigned_officer_id')->count() . "\n";
    echo "  with fiscal year:      " . DB::table('packages')->whereNotNull('fiscal_year')->count() . "\n\n";

    echo "By officer:\n";
    $rows = DB::table('packages as p')->join('officers as o', 'o.id', '=', 'p.assigned_officer_id')
        ->select('o.name', DB::raw('COUNT(*) as c'))->groupBy('o.name')->orderBy('o.name')->get();
    foreach ($rows as $r) echo "  " . str_pad($r->name, 20) . " {$r->c}\n";
    if ($rows->isEmpty()) echo "  (none)\n";

    echo "By fiscal year:\n";
    $rows = DB::table('packages')->whereNotNull('fiscal_year')
        ->select('fiscal_year', DB::raw('COUNT(*) as c'))->groupBy('fiscal_year')->orderBy('fiscal_year')->get();
    foreach ($rows as $r) echo "  " . str_pad($r->fiscal_year, 20) . " {$r->c}\n";
    if ($rows->isEmpty()) echo "  (none)\n";
}

try {
    $action = $_GET['action'] ?? null;

    if ($action === 'seed') {
        $officerIds = DB::table('officers')->pluck('id')->all();
        if (empty($officerIds)) {
            exit("No officers found — add officers first (same list used by Add Requisition).\n");
        }
        $fiscalYears = App\Http\Controllers\PackageController::fiscalYearOptions();

        $count = 0;
        foreach (DB::table('packages')->pluck('id') as $pkgId) {
            DB::table('packages')->where('id', $pkgId)->update([
                'assigned_officer_id' => $officerIds[array_rand($officerIds)],
                'fiscal_year'         => $fiscalYears[array_rand($fiscalYears)],
            ]);
            $count++;
        }
        echo "SEEDED: {$count} packages got a random officer + fiscal year.\n\n";
        showState();
    } elseif ($action === 'clear') {
        $count = DB::table('packages')->update([
            'assigned_officer_id' => null,
            'fiscal_year'         => null,
        ]);
        echo "CLEARED: officer & fiscal year removed from {$count} packages.\n\n";
        showState();
    } else {
        showState();
        echo "\n== Actions ==\n";
        echo "Add &action=seed  to the URL → fill all packages with random test data\n";
        echo "Add &action=clear to the URL → wipe both fields (undo test data)\n";
        echo "Add &delete=1     to the URL → remove this script from the server\n";
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "FAILED: " . $e->getMessage() . "\n";
    echo "Did you run the migrations first? (run-migrations.php)\n";
}
