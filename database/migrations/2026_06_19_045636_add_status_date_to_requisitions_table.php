<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            // Date captured for the selected requisition status when that status
            // does not have its own dedicated date column (e.g. Initiate,
            // Tender Opened, Evaluation Completed). Contract Signed -> signing_date
            // and Delivered -> delivery_date remain as-is.
            $table->date('status_date')->nullable()->after('requisition_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('status_date');
        });
    }
};
