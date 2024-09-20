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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('server_id')->constrained('service_servers')->onDelete('cascade');
            $table->string('orderProviderName')->nullable();
            $table->string('orderProviderServer')->nullable();
            $table->string('order_package')->nullable();
            $table->string('object_server')->nullable();
            $table->string('object_id')->nullable();
            $table->longText('order_id')->nullable();
            $table->longText('order_code')->nullable();
            $table->longText('order_data')->nullable();
            $table->longText('start')->nullable();
            $table->longText('buff')->nullable();
            $table->longText('duration')->nullable();
            // còn lại
            $table->longText('remaining')->nullable(); // số lượng, hoặc hết hạn

            $table->longText('posts')->nullable();
            $table->longText('price')->nullable();
            $table->longText('payment')->nullable();
            $table->string('status')->default('Processing'); // Processing, Completed, Cancelled, Refunded, Failed, Pending, Partially Refunded, Partially Completed
            $table->string('ip')->nullable();
            $table->longText('note')->nullable();
            $table->longText('error')->nullable();
            $table->longText('time')->nullable();
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
