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
        Schema::table('users', function (Blueprint $table) {
            //change password column to nullable
            $table->string('password')->nullable(true)->change();
            $table->string('username', 50)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('cover_photo_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('role')->default('student');
            $table->boolean('is_verified')->default(false);
            $table->integer('point')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('profession')->nullable();
            $table->unsignedInteger('preference')->nullable();
            $table->json('social_media')->nullable();
            $table->decimal('price', 9, 2)->nullable()->unsigned();
            $table->decimal('lifetime_price', 9, 2)->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //opposite of up
            $table->string('password')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'avatar_url',
                'bio',
                'role',
                'is_verified',
                'point',
                'last_login_at',
                'profession',
                'preference',
            ]);
        });
    }
};
