<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrateInvoicesUserIdToUuid();
        $this->migrateSubscriptionsUserIdToUuid();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->revertInvoicesUserIdToForeignId();
        $this->revertSubscriptionsUserIdToForeignId();
    }

    private function migrateInvoicesUserIdToUuid(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'number']);
            $table->uuid('user_id_tmp')->nullable()->after('id');
        });

        DB::table('invoices')->update([
            'user_id_tmp' => DB::raw('user_id'),
        ]);

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('user_id_tmp', 'user_id');
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'number']);
        });
    }

    private function migrateSubscriptionsUserIdToUuid(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->uuid('user_id_tmp')->nullable()->after('id');
        });

        DB::table('subscriptions')->update([
            'user_id_tmp' => DB::raw('user_id'),
        ]);

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('user_id_tmp', 'user_id');
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    private function revertInvoicesUserIdToForeignId(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'number']);
            $table->unsignedBigInteger('user_id_tmp')->nullable()->after('id');
        });

        $this->copyUuidToInteger('invoices');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('user_id_tmp', 'user_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'number']);
        });
    }

    private function revertSubscriptionsUserIdToForeignId(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id_tmp')->nullable()->after('id');
        });

        $this->copyUuidToInteger('subscriptions');

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('user_id_tmp', 'user_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    private function copyUuidToInteger(string $table): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("UPDATE {$table} SET user_id_tmp = CAST(user_id AS UNSIGNED)");

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("UPDATE {$table} SET user_id_tmp = CAST(user_id AS BIGINT)");

            return;
        }

        DB::statement("UPDATE {$table} SET user_id_tmp = CAST(user_id AS INTEGER)");
    }
};
