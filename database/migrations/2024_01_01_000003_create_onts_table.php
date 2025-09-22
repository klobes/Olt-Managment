<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->foreignId('port_id')->constrained()->onDelete('cascade');
            $table->integer('ont_id');
            $table->string('serial_number')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('model')->nullable();
            $table->string('firmware_version')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('admin_status')->default(true);
            $table->boolean('operational_status')->default(false);
            $table->decimal('rx_power', 5, 2)->nullable();
            $table->decimal('tx_power', 5, 2)->nullable();
            $table->decimal('distance', 8, 2)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('customer_info')->nullable();
            $table->json('service_profile')->nullable();
            $table->json('vlan_config')->nullable();
            $table->json('bandwidth_profile')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['device_id', 'port_id', 'ont_id']);
            $table->index(['device_id', 'status']);
            $table->index('serial_number');
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onts');
    }
};