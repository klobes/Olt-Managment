<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for multi-vendor support
     */
    public function up(): void
    {
        // Add vendor field to olt_devices table
        Schema::table('olt_devices', function (Blueprint $table) {
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other'])->default('fiberhome')->after('name');
            $table->string('model')->nullable()->after('vendor');
            $table->string('firmware_version')->nullable()->after('model');
            
            $table->index('vendor');
        });

        // Vendor-specific configurations
        Schema::create('vendor_configurations', function (Blueprint $table) {
            $table->id();
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other']);
            $table->string('model')->nullable();
            $table->json('oid_mappings'); // Vendor-specific OID mappings
            $table->json('capabilities'); // Supported features
            $table->json('default_settings')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['vendor', 'model']);
        });

        // Vendor-specific ONU types
        Schema::create('onu_types', function (Blueprint $table) {
            $table->id();
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other']);
            $table->string('model');
            $table->string('type_name');
            $table->integer('ethernet_ports')->default(0);
            $table->integer('pots_ports')->default(0);
            $table->integer('catv_ports')->default(0);
            $table->boolean('wifi_support')->default(false);
            $table->json('capabilities')->nullable();
            $table->json('default_config')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['vendor', 'model']);
        });

        // Add vendor and model to onus table
        Schema::table('onus', function (Blueprint $table) {
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other'])->default('fiberhome')->after('onu_name');
            $table->string('model')->nullable()->after('vendor');
            $table->foreignId('onu_type_id')->nullable()->after('model')->constrained()->onDelete('set null');
            $table->string('firmware_version')->nullable()->after('onu_type_id');
            
            $table->index('vendor');
        });

        // Vendor-specific commands/templates
        Schema::create('vendor_command_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other']);
            $table->string('command_name');
            $table->string('command_category'); // configuration, monitoring, troubleshooting
            $table->text('command_template'); // Template with placeholders
            $table->json('parameters')->nullable(); // Parameter definitions
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['vendor', 'command_category']);
        });

        // Vendor-specific service profiles
        Schema::create('vendor_service_profiles', function (Blueprint $table) {
            $table->id();
            $table->enum('vendor', ['fiberhome', 'huawei', 'zte', 'other']);
            $table->string('profile_name');
            $table->string('profile_type'); // bandwidth, vlan, qos, etc.
            $table->json('configuration'); // Vendor-specific config
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['vendor', 'profile_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_service_profiles');
        Schema::dropIfExists('vendor_command_templates');
        
        Schema::table('onus', function (Blueprint $table) {
            $table->dropForeign(['onu_type_id']);
            $table->dropColumn(['vendor', 'model', 'onu_type_id', 'firmware_version']);
        });
        
        Schema::dropIfExists('onu_types');
        Schema::dropIfExists('vendor_configurations');
        
        Schema::table('olt_devices', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'model', 'firmware_version']);
        });
    }
};