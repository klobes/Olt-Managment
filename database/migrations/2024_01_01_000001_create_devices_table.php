<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other'])->default('other');
            $table->string('model')->nullable();
            $table->ipAddress('ip_address');
            $table->string('snmp_community')->default('public');
            $table->enum('snmp_version', ['1', '2c', '3'])->default('2c');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('mac_address')->nullable();
            $table->integer('port_count')->default(16);
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['vendor', 'status']);
            $table->index('ip_address');
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};