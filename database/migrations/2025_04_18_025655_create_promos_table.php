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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->text('description');
            $table->string('discount_type')->default('percentage');
            $table->string('discount_value');
            $table->string('applies_to')->default('class');
            $table->integer('target_id')->nullable();
            $table->decimal('min_purchase', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('max_usage_total')->comment('Total times this promo can be used');
            $table->integer('max_usage_per_user')->default(1);
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
