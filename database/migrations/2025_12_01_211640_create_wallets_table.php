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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('balance')->default(0);
            $table->string('currency')->default('USD');
            $table->enum('wallet_type', ['primary', 'savings'])->default('primary');
            $table->string('pin')->nullable();
            $table->integer('pin_attempts')->default(0);
            $table->integer('failed_pin_attempts')->default(0);
            $table->timestamp('pin_locked_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
