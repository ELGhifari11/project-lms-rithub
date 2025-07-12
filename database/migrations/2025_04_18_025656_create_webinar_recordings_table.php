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
        Schema::create('webinar_recordings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mentor_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->boolean('is_preview')->default(false);
            $table->text('thumbnail_path')->nullable();
            $table->string('title');
            $table->text('content_path');
            $table->text('description');
            $table->biginteger('price')->default(0)->unsigned();
            $table->bigInteger('lifetime_price')->default(0)->unsigned();
            $table->integer('views')->default(0);
            $table->integer('duration')->default(0);
            $table->boolean('status');
            $table->dateTime('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webinar_recordings');
    }
};
