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
        Schema::create('class_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mentor_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->text('thumbnail_path')->nullable();
            $table->string('title');
            $table->text('description');
            $table->integer('duration_minutes');
            $table->biginteger('price')->default(0)->unsigned();
            $table->bigInteger('lifetime_price')->default(0)->unsigned();
            $table->boolean('status');
            $table->timestamps();

            $table->index(['title', 'mentor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_models');
    }
};
