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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');
            $table->string('trade_name');
            $table->double('price');
            $table->string('company');
            $table->integer('quantity_avaliable');
            $table->string('expiration_date');
            $table->unsignedBigInteger('medicine_wherehouse_id');
            $table->foreign('medicine_wherehouse_id')->references('id')->on('medicine_wherehouses');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
