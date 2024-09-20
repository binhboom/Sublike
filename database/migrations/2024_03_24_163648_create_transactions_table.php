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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tran_code')->unique();
            $table->string('type')->nullable();
            $table->enum('action', ['add', 'sub'])->default('add');
            $table->longText('first_balance')->nullable();
            $table->longText('before_balance')->nullable();
            $table->longText('after_balance')->nullable();
            $table->longText('note')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
