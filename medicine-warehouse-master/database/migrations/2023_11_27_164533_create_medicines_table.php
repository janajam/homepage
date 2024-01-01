<?php

use App\Models\Category;
use App\Models\Warehouse;
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
            $table->string('trade_name')->unique();
            $table->integer('price');
            $table->string('company');
            $table->integer('quantity_available') ;
            $table->integer('quantity_for_sale') ;
            $table->dateTime('expiration_date');
            $table->string('image')->nullable() ;
            $table->integer('sold_out')->default(0) ;
            $table->foreignIdFor(Warehouse::class)->constrained();
            $table->foreignIdFor(Category::class)->constrained();
            $table->timestamps();
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
