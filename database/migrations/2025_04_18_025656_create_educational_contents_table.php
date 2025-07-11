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
        Schema::create('educational_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_of_course_id')->nullable();
            $table->boolean('is_preview')->default(false);
            $table->string('type')->default('video');
            $table->string('title_content');
            $table->integer('order_index')->default(0); ;
            $table->text('content_path');
            $table->text('thumbnail_path');
            $table->integer('duration')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_contents');
    }
};
