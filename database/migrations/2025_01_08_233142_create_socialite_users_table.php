<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('socialite_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('provider');
            $table->string('provider_id');

            $table->timestamps();

            $table->unique([
                'provider',
                'provider_id',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('socialite_users');
    }
};
