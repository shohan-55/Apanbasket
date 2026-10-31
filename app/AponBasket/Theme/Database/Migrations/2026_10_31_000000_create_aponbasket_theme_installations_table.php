<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aponbasket_theme_installations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('channel_id')->unique();
            $table->string('theme_version', 32);
            $table->timestamp('installed_at');
            $table->foreign('channel_id')->references('id')->on('channels')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aponbasket_theme_installations');
    }
};
