<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // 2W, 3W
            $table->timestamps();
        });

        Schema::create('vehicle_variants', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('model_name');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types');
            $table->string('variant_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Dealer, RBC, RFB, Showroom, Institutional
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no')->unique();
            $table->string('chassis_no')->unique();
            $table->string('engine_no')->unique();
            $table->foreignId('vehicle_variant_id')->constrained('vehicle_variants')->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('cascade');
            $table->string('color')->nullable();
            $table->foreignId('sale_type_id')->constrained('sale_types')->onDelete('cascade');
            $table->dateTime('sale_date');
            $table->string('source_system')->nullable(); // DMS, Manual, etc.
            $table->boolean('is_provisional')->default(false);
            $table->boolean('is_validated')->default(false);
            $table->dateTime('validation_date')->nullable();
            $table->text('validation_notes')->nullable();
            $table->timestamps();
            $table->index('registration_no');
            $table->index('chassis_no');
            $table->index('engine_no');
        });

        Schema::create('vehicle_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->dateTime('registration_date');
            $table->string('registration_authority')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_service_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->integer('service_number');
            $table->dateTime('service_date');
            $table->string('branch_code')->nullable();
            $table->integer('odometer_reading')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['vehicle_id', 'service_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_service_history');
        Schema::dropIfExists('vehicle_registrations');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('sale_types');
        Schema::dropIfExists('vehicle_variants');
        Schema::dropIfExists('vehicle_types');
    }
};
