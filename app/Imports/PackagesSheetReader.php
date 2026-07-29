<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Read-only pass over an uploaded packages sheet.
 *
 * Used by PackageController::bulkUpload to inspect every row before the real
 * import runs, so all bad rows can be reported in one go — PackagesImport
 * inserts row-by-row and aborts on the first failure.
 */
class PackagesSheetReader implements WithHeadingRow
{
}
