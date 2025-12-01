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
        Schema::create('transaction_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('daily_limit', 15, 2)->default(1000000.00);
            $table->decimal('daily_spent', 15, 2)->default(0.00);
            $table->decimal('single_transaction_limit', 15, 2)->default(100000.00);
            $table->integer('daily_transaction_count')->default(0);
            $table->integer('max_daily_transactions')->default(50);
            $table->date('limit_date');
            $table->timestamps();

            $table->unique(['user_id', 'limit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_limits');
    }
};
