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
        Schema::create('service_platforms', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(0);
            $table->string('code')->unique();
            $table->string('name');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('slug')->unique();
            $table->longText('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_platforms');
    }
};
