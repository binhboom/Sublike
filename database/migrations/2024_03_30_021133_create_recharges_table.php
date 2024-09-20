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
        Schema::create('recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete('cascade');
            $table->string('order_code');
            $table->enum('type', ['bill', 'recharge'])->default('recharge');
            $table->string('payment_method'); // bank, momo, zalo, airpay, vnpay
            $table->string('bank_name')->nullable(); // bank name
            $table->string('bank_code')->nullable(); // bank code
            $table->longText('amount')->nullable();
            $table->longText('real_amount')->nullable();
            $table->enum('status', ['Success', 'Pending', 'Failed'])->default('Success');
            $table->longText('note')->nullable();
            $table->boolean('is_send_telegram')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamp('paid_at')->nullable(); // thời gian thanh toán
            $table->timestamp('expired_at')->nullable(); // thời gian hết hạn
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharges');
    }
};
