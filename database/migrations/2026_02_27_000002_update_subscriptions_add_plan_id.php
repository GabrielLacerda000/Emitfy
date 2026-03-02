<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['plan', 'provider', 'provider_subscription_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_id')->after('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('plan');
            $table->string('provider');
            $table->string('provider_subscription_id');
        });
    }
};
