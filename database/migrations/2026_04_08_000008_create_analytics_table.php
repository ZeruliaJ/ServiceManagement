<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code')->unique();
            $table->string('branch_name');
            $table->string('region')->nullable();
            $table->string('zone')->nullable();
            $table->string('district')->nullable();
            $table->string('town')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('job_card_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches');
            $table->dateTime('check_in_date');
            $table->dateTime('job_open_date')->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->dateTime('gate_out_date')->nullable();
            
            // TAT Calculations (in hours)
            $table->integer('tat_check_in_to_open')->nullable();
            $table->integer('tat_open_to_completion')->nullable();
            $table->integer('tat_completion_to_delivery')->nullable();
            $table->integer('tat_delivery_to_gate_out')->nullable();
            $table->integer('tat_total')->nullable();
            
            $table->timestamps();
        });

        Schema::create('vehicle_lifetime_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->integer('total_service_visits');
            $table->decimal('total_service_cost', 12, 2);
            $table->decimal('total_parts_cost', 12, 2);
            $table->decimal('total_labour_cost', 12, 2);
            $table->decimal('total_warranty_claims', 12, 2);
            $table->dateTime('first_service_date')->nullable();
            $table->dateTime('last_service_date')->nullable();
            $table->integer('average_service_interval_days')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_lifetime_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->integer('total_vehicles_serviced');
            $table->decimal('lifetime_value', 12, 2);
            $table->integer('total_job_cards');
            $table->dateTime('first_service_date')->nullable();
            $table->dateTime('last_service_date')->nullable();
            $table->integer('repeat_visits')->default(0);
            $table->decimal('average_visit_value', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('daily_branch_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->dateTime('summary_date');
            $table->integer('pending_count');
            $table->integer('in_progress_count');
            $table->integer('completed_count');
            $table->integer('delivered_count');
            $table->decimal('daily_revenue', 12, 2);
            $table->decimal('warranty_claims_value', 12, 2);
            $table->integer('free_services_count');
            $table->timestamps();
            $table->unique(['branch_id', 'summary_date']);
        });

        Schema::create('repeat_repair_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('defect_type')->nullable();
            $table->integer('repeat_count');
            $table->dateTime('first_occurrence_date');
            $table->dateTime('last_occurrence_date');
            $table->decimal('total_cost', 12, 2);
            $table->text('analysis_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repeat_repair_analysis');
        Schema::dropIfExists('daily_branch_summary');
        Schema::dropIfExists('customer_lifetime_value');
        Schema::dropIfExists('vehicle_lifetime_data');
        Schema::dropIfExists('job_card_metrics');
        Schema::dropIfExists('branches');
    }
};
