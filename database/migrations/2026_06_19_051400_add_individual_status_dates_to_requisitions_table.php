<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each requisition status gets its own execution-date column so dates are
     * stored individually (Contract Signed -> signing_date and Delivered ->
     * delivery_date already exist). The old single status_date is migrated
     * into the matching column and then dropped.
     */
    public function up(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->date('initiate_date')->nullable()->after('requisition_status_id');
            $table->date('tender_opened_date')->nullable()->after('initiate_date');
            $table->date('evaluation_completed_date')->nullable()->after('tender_opened_date');
        });

        // Move any existing single status_date into the right per-status column.
        if (Schema::hasColumn('requisitions', 'status_date')) {
            $map = [
                'Initiate'             => 'initiate_date',
                'Tender Opened'        => 'tender_opened_date',
                'Evaluation Completed' => 'evaluation_completed_date',
            ];

            foreach ($map as $statusName => $column) {
                $statusId = DB::table('requisition_statuses')->where('name', $statusName)->value('id');
                if ($statusId) {
                    DB::table('requisitions')
                        ->where('requisition_status_id', $statusId)
                        ->whereNotNull('status_date')
                        ->update([$column => DB::raw('status_date')]);
                }
            }

            Schema::table('requisitions', function (Blueprint $table) {
                $table->dropColumn('status_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->date('status_date')->nullable()->after('requisition_status_id');
        });

        $map = [
            'Initiate'             => 'initiate_date',
            'Tender Opened'        => 'tender_opened_date',
            'Evaluation Completed' => 'evaluation_completed_date',
        ];
        foreach ($map as $statusName => $column) {
            $statusId = DB::table('requisition_statuses')->where('name', $statusName)->value('id');
            if ($statusId) {
                DB::table('requisitions')
                    ->where('requisition_status_id', $statusId)
                    ->whereNotNull($column)
                    ->update(['status_date' => DB::raw($column)]);
            }
        }

        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn(['initiate_date', 'tender_opened_date', 'evaluation_completed_date']);
        });
    }
};
