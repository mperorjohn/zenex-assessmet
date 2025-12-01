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
        Schema::table('wallets', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_active');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->string('locked_by')->nullable()->after('locked_at');
            $table->string('balance_checksum')->nullable()->after('balance');
            $table->integer('max_pin_attempts')->default(3)->after('failed_pin_attempts');
            $table->integer('lockout_duration_minutes')->default(30)->after('max_pin_attempts');
            $table->timestamp('last_failed_attempt_at')->nullable()->after('lockout_duration_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'is_locked',
                'locked_at',
                'locked_by',
                'balance_checksum',
                'max_pin_attempts',
                'lockout_duration_minutes',
                'last_failed_attempt_at',
            ]);
        });
    }
};
