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
        Schema::table('rab_process_items', function (Blueprint $table) {
            $table->decimal('profit', 5, 2)->default(0)->after('subtotal');
            $table->decimal('overhead', 5, 2)->default(0)->after('profit');
            // $table->dropColumn(['profit_percent', 'overhead_percent', 'overhead_value', 'profit_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rab_process_items', function (Blueprint $table) {
            
        });
    }
};
