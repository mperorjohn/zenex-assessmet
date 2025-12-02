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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_wallet_id')->nullable()->constrained('wallets')->onDelete('cascade');
            $table->foreignId('receiver_wallet_id')->nullable()->constrained('wallets')->onDelete('cascade');
            $table->enum('transaction_type', ['wallet_to_wallet', 'fund_wallet', 'purchase', 'top_up', 'withdrawal']);
            $table->unsignedBigInteger('amount');
            $table->string('currency')->default('USD');
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
