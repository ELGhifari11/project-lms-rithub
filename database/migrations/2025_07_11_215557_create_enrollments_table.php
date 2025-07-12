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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->morphs('enrollable');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('status')->default('enrolled');
            $table->integer('progress')->default(0);
            $table->boolean('is_certificate_issued')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'enrollable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
