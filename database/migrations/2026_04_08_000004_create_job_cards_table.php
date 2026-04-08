<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Free, Paid, Warranty, Goodwill, Campaign
            $table->timestamps();
        });

        Schema::create('job_card_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Pending, In Progress, Completed, Delivered
            $table->timestamps();
        });

        Schema::create('free_service_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_no')->unique();
            $table->foreignId('vehicle_variant_id')->constrained('vehicle_variants');
            $table->integer('service_number');
            $table->dateTime('issued_date');
            $table->dateTime('expiry_date');
            $table->boolean('is_used')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->string('job_card_no')->unique();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');
            $table->foreignId('job_card_status_id')->constrained('job_card_statuses')->onDelete('cascade');
            $table->foreignId('customer_party_id')->constrained('parties')->onDelete('cascade');
            $table->foreignId('bill_to_party_id')->constrained('parties')->onDelete('cascade');
            $table->foreignId('free_service_coupon_id')->nullable()->constrained('free_service_coupons')->onDelete('cascade');
            $table->dateTime('check_in_date');
            $table->integer('odometer_in')->nullable();
            $table->integer('odometer_out')->nullable();
            $table->string('fuel_level_in')->nullable();
            $table->string('fuel_level_out')->nullable();
            $table->text('customer_complaints')->nullable();
            $table->dateTime('estimated_delivery_date');
            $table->dateTime('actual_delivery_date')->nullable();
            $table->string('priority'); // Normal, Urgent, Emergency
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users');
            $table->text('supervisor_notes')->nullable();
            $table->text('technician_remarks')->nullable();
            $table->timestamps();
            $table->index('job_card_no');
        });

        Schema::create('job_card_standard_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->string('check_item');
            $table->enum('status', ['OK', 'Not OK'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('job_card_after_trial_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->string('check_item');
            $table->enum('status', ['OK', 'Not OK'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_card_after_trial_checks');
        Schema::dropIfExists('job_card_standard_checks');
        Schema::dropIfExists('job_cards');
        Schema::dropIfExists('free_service_coupons');
        Schema::dropIfExists('job_card_statuses');
        Schema::dropIfExists('service_types');
    }
};
