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
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });

        Schema::table('socialite_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('class_models', function (Blueprint $table) {
            $table->foreign('mentor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->restrictOnDelete();
        });

        Schema::table('module_of_courses', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('subscription_id')->references('id')->on('user_subscriptions')->nullOnDelete();
        });

        Schema::table('educational_contents', function (Blueprint $table) {
            $table->foreign('module_of_course_id')->references('id')->on('module_of_courses')->cascadeOnDelete();
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
