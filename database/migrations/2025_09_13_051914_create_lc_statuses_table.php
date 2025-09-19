<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lc_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();   // e.g. Opened, Draft Issued, Sent to Bank
            $table->string('code')->nullable(); // optional short form (OPEN, DRAFT, SENT)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_statuses');
    }
};
