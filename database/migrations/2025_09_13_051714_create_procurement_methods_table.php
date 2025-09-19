<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();   // RFQ, OTM, DPM
            $table->string('code')->nullable(); // optional short form
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_methods');
    }
};
