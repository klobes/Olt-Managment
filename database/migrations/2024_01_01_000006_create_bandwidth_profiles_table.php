<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bandwidth_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_device_id')->constrained()->onDelete('cascade');
            $table->integer('profile_id');
            $table->string('profile_name');
            $table->integer('up_min_rate')->default(0); // kbps
            $table->integer('up_max_rate')->default(0); // kbps
            $table->integer('down_min_rate')->default(0); // kbps
            $table->integer('down_max_rate')->default(0); // kbps
            $table->integer('fixed_rate')->nullable(); // kbps
            $table->timestamps();
            
            $table->unique(['olt_device_id', 'profile_id']);
            $table->index('profile_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandwidth_profiles');
    }
};