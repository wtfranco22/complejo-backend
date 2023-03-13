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
            $table->id();
            $table->foreignId('rol_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('account_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('active')->default(true);
            $table->string('name', 100);
            $table->string('lastname', 100);
            $table->unsignedInteger('dni')->unique();
            $table->unsignedInteger('phone')->nullable(false);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('address', 250);
            $table->string('password', 50);
            $table->rememberToken();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
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