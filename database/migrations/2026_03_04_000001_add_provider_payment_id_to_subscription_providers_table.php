<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_providers', function (Blueprint $table) {
            $table->string('provider_payment_id')->nullable()->after('provider_subscription_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_providers', function (Blueprint $table) {
            $table->dropColumn('provider_payment_id');
        });
    }
};
