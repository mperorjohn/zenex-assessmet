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
            $table->unsignedBigInteger('daily_limit')->default(100000000);
            $table->unsignedBigInteger('daily_spent')->default(0);
            $table->unsignedBigInteger('single_transaction_limit')->default(10000000);
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
