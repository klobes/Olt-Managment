<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // temperature, power, cpu, memory, etc.
            $table->string('metric_name');
            $table->decimal('value', 10, 4);
            $table->string('unit')->nullable();
            $table->decimal('threshold_min', 10, 4)->nullable();
            $table->decimal('threshold_max', 10, 4)->nullable();
            $table->enum('status', ['normal', 'warning', 'critical'])->default('normal');
            $table->enum('alert_level', ['info', 'warning', 'critical'])->default('info');
            $table->text('message')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['device_id', 'recorded_at']);
            $table->index(['metric_type', 'recorded_at']);
            $table->index(['status', 'alert_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_logs');
    }
};