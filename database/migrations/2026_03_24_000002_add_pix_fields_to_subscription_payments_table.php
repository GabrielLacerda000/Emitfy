<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->text('pix_code')->nullable()->after('raw_payload');
            $table->text('qr_code_base64')->nullable()->after('pix_code');
            $table->timestamp('expires_at')->nullable()->after('qr_code_base64');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropColumn(['pix_code', 'qr_code_base64', 'expires_at']);
        });
    }
};
