<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();   // optional short form (e.g., REG, EMG)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_types');
    }
};
