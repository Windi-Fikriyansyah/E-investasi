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
            $table->bigIncrements('id'); // BIGINT auto increment
            $table->string('name', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password', 255);
            $table->string('referral_code', 20)->unique();
            $table->string('referred_by', 20)->nullable();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->decimal('balance', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken(); // Untuk "remember me" token
            $table->timestamps(); // created_at dan updated_at
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
