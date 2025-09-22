<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->integer('port_number');
            $table->enum('port_type', ['GPON', 'EPON', 'GE', 'FE', '10GE'])->default('GPON');
            $table->boolean('status')->default(false);
            $table->boolean('admin_status')->default(true);
            $table->boolean('operational_status')->default(false);
            $table->string('speed')->nullable();
            $table->string('duplex')->nullable();
            $table->integer('vlan_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('rx_power', 5, 2)->nullable();
            $table->decimal('tx_power', 5, 2)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('voltage', 5, 2)->nullable();
            $table->decimal('current', 5, 2)->nullable();
            $table->integer('ont_count')->default(0);
            $table->integer('max_ont_count')->default(128);
            $table->timestamps();
            
            $table->unique(['device_id', 'port_number']);
            $table->index(['device_id', 'status']);
            $table->index('port_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};