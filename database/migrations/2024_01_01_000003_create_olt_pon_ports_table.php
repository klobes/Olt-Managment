<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('olt_pon_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_device_id')->constrained()->onDelete('cascade');
            $table->foreignId('olt_card_id')->constrained()->onDelete('cascade');
            $table->integer('pon_index');
            $table->string('pon_name');
            $table->text('description')->nullable();
            $table->integer('pon_type')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->enum('online_status', ['online', 'offline'])->default('offline');
            $table->integer('speed')->nullable();
            $table->integer('upstream_speed')->nullable();
            $table->integer('tx_optical_power')->nullable();
            $table->integer('optical_voltage')->nullable();
            $table->integer('optical_current')->nullable();
            $table->integer('optical_temperature')->nullable();
            $table->integer('auth_onu_num')->default(0);
            $table->timestamps();
            
            $table->unique(['olt_device_id', 'pon_index']);
            $table->index('online_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('olt_pon_ports');
    }
};