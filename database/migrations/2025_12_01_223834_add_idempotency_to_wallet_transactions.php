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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('idempotency_key')->unique()->nullable()->after('reference');
            $table->timestamp('idempotency_expires_at')->nullable()->after('idempotency_key');
            $table->timestamp('initiated_at')->nullable()->after('description');
            $table->timestamp('processed_at')->nullable()->after('initiated_at');
            $table->timestamp('completed_at')->nullable()->after('processed_at');
            $table->timestamp('failed_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'idempotency_key',
                'idempotency_expires_at',
                'initiated_at',
                'processed_at',
                'completed_at',
                'failed_at',
            ]);
        });
    }
};
