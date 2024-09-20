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
        Schema::create('recharge_cards', function (Blueprint $table) {
            $table->id();
            $table->longText('code')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->nullable();
            $table->longText('amount')->nullable();
            $table->longText('real_amount')->nullable();
            $table->string('serial')->nullable();
            $table->string('pin')->nullable();
            $table->enum('status', ['success', 'pending', 'failed'])->default('pending');
            $table->longText('tran_id')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharge_cards');
    }
};
