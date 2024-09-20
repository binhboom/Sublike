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
        Schema::create('server_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('service_servers')->onDelete('cascade');
            $table->enum('get_uid', ['on', 'off'])->default('off');
            $table->enum('quantity_status', ['on', 'off'])->default('on');
            $table->enum('reaction_status', ['on', 'off'])->default('off');
            $table->longText('reaction_data')->nullable();
            $table->enum('comments_status', ['on', 'off'])->default('off');
            $table->longText('comments_data')->nullable();
            $table->enum('minutes_status', ['on', 'off'])->default('off');
            $table->longText('minutes_data')->nullable();
            $table->enum('posts_status', ['on', 'off'])->default('off');
            $table->longText('posts_data')->nullable();
            $table->enum('time_status', ['on', 'off'])->default('off'); // thời gian
            $table->longText('time_data')->nullable();
            $table->enum('duration_status', ['on', 'off'])->default('off');
            $table->longText('duration_data')->nullable();
            $table->enum('refund_status', ['on', 'off'])->default('off');
            $table->enum('warranty_status', ['on', 'off'])->default('off');
            $table->enum('renews_status', ['on', 'off'])->default('off');
            $table->enum('renew_type', ['auto', 'manual'])->default('auto');
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_actions');
    }
};
