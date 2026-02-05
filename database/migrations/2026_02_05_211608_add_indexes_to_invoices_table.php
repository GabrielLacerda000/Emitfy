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
        Schema::table('invoices', function (Blueprint $table) {
            // Composite index for filtering invoices by user and status
            $table->index(['user_id', 'status']);

            // Composite index for invoice number queries per user
            $table->index(['user_id', 'number']);

            // Index for public invoice lookup
            $table->index('public_token');

            // Index for overdue detection
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'number']);
            $table->dropIndex(['public_token']);
            $table->dropIndex(['due_date']);
        });
    }
};
