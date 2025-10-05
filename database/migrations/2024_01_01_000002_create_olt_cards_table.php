<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('olt_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_device_id')->constrained()->onDelete('cascade');
            $table->integer('slot_index');
            $table->integer('card_type');
            $table->string('card_type_name')->nullable();
            $table->string('hardware_version')->nullable();
            $table->string('software_version')->nullable();
            $table->enum('status', ['normal', 'offline', 'error'])->default('offline');
            $table->integer('num_of_ports')->default(0);
            $table->integer('available_ports')->default(0);
            $table->integer('cpu_util')->nullable();
            $table->integer('mem_util')->nullable();
            $table->timestamps();
            
            $table->unique(['olt_device_id', 'slot_index']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('olt_cards');
    }
};