<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained('warranties')->onDelete('cascade');
            $table->foreignId('job_card_id')->nullable()->constrained('job_cards')->onDelete('cascade');
            $table->dateTime('validation_date');
            $table->string('validation_status'); // Valid, Expired, Void
            $table->text('validation_reason')->nullable();
            $table->boolean('claim_eligible')->default(false);
            $table->timestamps();
        });

        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained('warranties')->onDelete('cascade');
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->dateTime('claim_date');
            $table->decimal('claim_amount', 12, 2);
            $table->string('claim_status'); // Pending, Approved, Rejected
            $table->text('claim_reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->dateTime('approval_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('warranty_validations');
    }
};
