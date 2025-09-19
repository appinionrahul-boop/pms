<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approving_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();   // e.g. SE, CFO, MD, PM, CE
            $table->string('code')->nullable(); // optional short form
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approving_authorities');
    }
};
