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
        Schema::create('requisitions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();

            // right column in your mock
            $t->string('package_no')->index();                 // snapshot for convenience
            $t->foreignId('requisition_status_id')->nullable()->constrained('requisition_statuses')->nullOnDelete();
            $t->decimal('estimated_cost_bdt',16,2)->nullable();
            $t->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $t->decimal('quantity',16,3)->nullable();
            $t->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('procurement_type_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedBigInteger('assigned_officer_id')->nullable(); // users.id (single-user ok)
            $t->string('tech_spec_file')->nullable();          // uploaded excel path
            $t->foreignId('approving_authority_id')->nullable()->constrained('approving_authorities')->nullOnDelete();
            $t->date('signing_date')->nullable();
            $t->foreignId('lc_status_id')->nullable()->constrained('lc_statuses')->nullOnDelete();
            $t->string('reference_annex')->nullable();         // file path

            // left column in your mock
            $t->text('description')->nullable();
            $t->foreignId('procurement_method_id')->nullable()->constrained('procurement_methods')->nullOnDelete();
            $t->string('vendor_name')->nullable();
            $t->decimal('official_estimated_cost_bdt',16,2)->nullable();
            $t->date('requisition_receiving_date')->nullable();
            $t->date('delivery_date')->nullable();
            $t->string('reference_link')->nullable();
            $t->text('comments')->nullable();
            $t->text('officer_name')->nullable();

            $t->timestamps();
        });
    }

};
