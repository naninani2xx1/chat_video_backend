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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('access_token')->nullable();
            $table->string('open_id')->nullable();
            $table->string('token')->nullable();
            $table->dateTime('expire_date')->nullable();
            $table->integer('type')->nullable();
            $table->integer('online')->nullable();
            $table->string('email')->unique();
            $table->string('avatar');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
