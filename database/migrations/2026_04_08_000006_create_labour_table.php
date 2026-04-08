<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_code')->unique();
            $table->string('operation_name');
            $table->text('description')->nullable();
            $table->decimal('standard_labor_rate', 12, 2);
            $table->decimal('standard_hours', 8, 2);
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_code')->unique();
            $table->string('specialization')->nullable();
            $table->decimal('hourly_rate', 10, 2);
            $table->boolean('is_supervisor')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('job_card_labour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('operation_id')->constrained('labour_operations');
            $table->foreignId('technician_id')->constrained('technicians');
            $table->decimal('hours', 8, 2);
            $table->decimal('rate', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->foreignId('charge_type_id')->constrained('charge_types');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('job_card_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->string('signature_type'); // Supervisor, Customer, Delivery, GatePass
            $table->text('signature_data')->nullable(); // Base64 encoded
            $table->string('signed_by_name');
            $table->string('signed_by_id')->nullable();
            $table->dateTime('signed_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_card_signatures');
        Schema::dropIfExists('job_card_labour');
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('labour_operations');
    }
};
