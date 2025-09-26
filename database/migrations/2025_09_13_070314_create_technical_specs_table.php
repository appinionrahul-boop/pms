<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_specs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();
            $t->string('spec_name');
            $t->decimal('quantity', 16, 3)->nullable();
            $t->decimal('unit_price_bdt', 16, 2)->nullable();
            $t->decimal('total_price_bdt', 16, 2)->nullable(); // store computed
            $t->string('erp_code')->nullable();  // ✅ fixed variable name
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_specs');
    }
};

