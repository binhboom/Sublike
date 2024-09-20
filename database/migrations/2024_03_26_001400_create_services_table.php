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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(0);
            $table->string('code')->unique()->nullable();
            $table->string('name');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('note')->nullable(); //  ghi chú
            $table->longText('details')->nullable(); //  chi tiết
            $table->string('package')->unique()->nullable();
            $table->string('slug')->unique();
            $table->longText('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('platform_id')->constrained('service_platforms');
            $table->enum('reaction_status', ['on', 'off'])->default('off');
            $table->enum('quantity_status', ['on', 'off'])->default('off');
            $table->enum('comments_status', ['on', 'off'])->default('off');
            $table->enum('minutes_status', ['on', 'off'])->default('off');
            $table->enum('time_status', ['on', 'off'])->default('off');
            $table->enum('posts_status', ['on', 'off'])->default('off');
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
