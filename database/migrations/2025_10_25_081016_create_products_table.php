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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_id')->nullable()->constrained('cards')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_shopee')->nullable();
            $table->string('link_tiktok')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('range')->nullable(); // e.g., "001 – 250"
            $table->integer('stock')->nullable();
            $table->string('status')->default('active'); // active, inactive, coming_soon
            $table->text('specifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
