<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_device_id')->constrained()->onDelete('cascade');
            $table->foreignId('olt_pon_port_id')->constrained()->onDelete('cascade');
            $table->integer('onu_index');
            $table->string('onu_name');
            $table->text('description')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('password')->nullable();
            $table->integer('onu_type')->nullable();
            $table->enum('auth_type', ['mac', 'sn', 'both'])->default('mac');
            $table->boolean('is_enabled')->default(false);
            $table->enum('status', ['online', 'offline', 'los', 'dying_gasp'])->default('offline');
            $table->integer('distance')->nullable(); // meters
            $table->integer('rx_optical_power')->nullable();
            $table->integer('tx_optical_power')->nullable();
            $table->integer('optical_voltage')->nullable();
            $table->integer('optical_current')->nullable();
            $table->integer('optical_temperature')->nullable();
            $table->timestamp('last_online')->nullable();
            $table->timestamp('last_offline')->nullable();
            $table->timestamps();
            
            $table->unique(['olt_device_id', 'onu_index']);
            $table->index('mac_address');
            $table->index('serial_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onus');
    }
};