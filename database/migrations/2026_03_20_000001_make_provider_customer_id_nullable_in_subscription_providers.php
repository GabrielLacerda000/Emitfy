<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_providers', function (Blueprint $table) {
            $table->string('provider_customer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_providers', function (Blueprint $table) {
            $table->string('provider_customer_id')->nullable(false)->change();
        });
    }
};
