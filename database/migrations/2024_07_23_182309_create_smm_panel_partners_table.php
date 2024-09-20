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
        Schema::create('smm_panel_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('url_api')->nullable();
            $table->longText('api_token')->nullable();
            $table->longText('price_update')->nullable();
            $table->enum('status', ['on', 'off'])->default('on');
            $table->enum('update_price', ['on', 'off'])->default('on');
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smm_panel_partners');
    }
};
