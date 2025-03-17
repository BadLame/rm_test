<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique();
            $table->string('name');
            $table->string('surname');
            $table->string('password');
            $table->dateTime('blocked_at')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->jsonb('access')->nullable()
                ->comment('');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
