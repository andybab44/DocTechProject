<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert status column from ENUM to VARCHAR so new values can be added freely
        Schema::table('work_jobs', function (Blueprint $table) {
            $table->string('status', 50)->default('awaiting_acceptance')->change();
        });

        // Migrate legacy values to the new names
        DB::table('work_jobs')->where('status', 'pending')->update(['status' => 'awaiting_acceptance']);
        DB::table('work_jobs')->where('status', 'done')->update(['status' => 'delivered']);
    }

    public function down(): void
    {
        // Reverse data migration first
        DB::table('work_jobs')->where('status', 'awaiting_acceptance')->update(['status' => 'pending']);
        DB::table('work_jobs')->where('status', 'delivered')->update(['status' => 'done']);

        // Revert column back to the original four ENUM values
        DB::statement("ALTER TABLE work_jobs MODIFY status ENUM('pending','in_progress','done','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
