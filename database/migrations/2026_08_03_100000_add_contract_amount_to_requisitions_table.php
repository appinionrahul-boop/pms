<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisitions', function (Blueprint $t) {
            $t->decimal('contract_amount', 16, 2)->nullable()->after('official_estimated_cost_bdt');
        });
    }

    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $t) {
            $t->dropColumn('contract_amount');
        });
    }
};
