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
        Schema::create('service_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('name');
            $table->longText('details')->nullable();
            $table->integer('package_id')->nullable();
            $table->longText('price')->nullable()->nullable();
            $table->longText('price_update')->nullable();
            $table->longText('price_member')->nullable()->nullable();
            $table->longText('price_collaborator')->nullable()->nullable();
            $table->longText('price_agency')->nullable()->nullable();
            $table->longText('price_distributor')->nullable()->nullable();
            $table->integer('min')->default(1);
            $table->integer('max')->default(1);
            $table->integer('limit_day')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->longText('providerLink')->nullable();
            $table->longText('providerServer')->nullable();
            $table->longText('providerName')->nullable();
            $table->longText('providerKey')->nullable();
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_servers');
    }
};
