<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('low_stock_threshold')
                ->constrained('vendors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Vendor::class);
            $table->dropColumn('vendor_id');
        });
    }
};
