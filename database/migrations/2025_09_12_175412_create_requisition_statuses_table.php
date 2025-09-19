<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();   // e.g. Initiated, Approved
            $table->string('code')->nullable(); // optional short code (INIT, APPR)
            $table->string('color')->nullable(); // optional for UI badge colors
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_statuses');
    }
};
