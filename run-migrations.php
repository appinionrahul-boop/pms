<?php
/**
 * One-time migration runner for cPanel deployments (no SSH needed).
 *
 * Usage after uploading/unzipping the project:
 *   https://YOUR-DOMAIN/run-migrations.php?key=44a781d7e9f55847af7654f5f3cf159a
 *
 * When everything is done, remove it from the server by visiting:
 *   https://YOUR-DOMAIN/run-migrations.php?key=44a781d7e9f55847af7654f5f3cf159a&delete=1
 *
 * SECURITY: delete this file as soon as the migration is complete.
 */

$SECRET_KEY = '44a781d7e9f55847af7654f5f3cf159a';

// Works whether this file sits in public/ (normal) or in the project root.
$root = is_file(__DIR__ . '/../vendor/autoload.php') ? dirname(__DIR__) : __DIR__;

header('Content-Type: text/plain; charset=utf-8');

if (!is_file($root . '/vendor/autoload.php')) {
    http_response_code(500);
    exit("ERROR: vendor/autoload.php not found.\nUpload the vendor folder (zip the whole project including vendor/).");
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
    exit(is_file(__FILE__) ? "Could not delete the file — remove it manually via File Manager.\n"
                           : "This script has deleted itself. Deployment tool removed.\n");
}

try {
    echo "== Database: " . Illuminate\Support\Facades\DB::connection()->getDatabaseName() . " ==\n\n";

    echo "== Migration status (before) ==\n";
    Illuminate\Support\Facades\Artisan::call('migrate:status');
    echo Illuminate\Support\Facades\Artisan::output();

    echo "\n== Running pending migrations ==\n";
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo Illuminate\Support\Facades\Artisan::output();

    echo "\n== Clearing caches ==\n";
    foreach (['config:clear', 'cache:clear', 'view:clear', 'route:clear'] as $cmd) {
        try {
            Illuminate\Support\Facades\Artisan::call($cmd);
            echo str_pad($cmd, 14) . ': ' . trim(Illuminate\Support\Facades\Artisan::output()) . "\n";
        } catch (Throwable $e) {
            echo str_pad($cmd, 14) . ': skipped (' . $e->getMessage() . ")\n";
        }
    }

    echo "\nALL DONE ✔\n";
    echo "Now delete this file! Visit this URL to self-delete:\n";
    echo "run-migrations.php?key={$SECRET_KEY}&delete=1\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "\nFAILED: " . $e->getMessage() . "\n";
    echo "Check the .env database settings on the server and try again.\n";
}
