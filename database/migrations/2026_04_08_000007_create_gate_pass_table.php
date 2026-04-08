<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Paid, Credit, Warranty_Claim_Pending, Finance_Claim_Pending
            $table->timestamps();
        });

        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Cash, Card, Bank Transfer, Wallet, Cheque
            $table->timestamps();
        });

        Schema::create('job_card_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->decimal('parts_total', 12, 2);
            $table->decimal('labour_total', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->foreignId('payment_status_id')->constrained('payment_statuses');
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->string('invoice_no')->nullable()->unique();
            $table->string('receipt_no')->nullable()->unique();
            $table->dateTime('payment_date')->nullable();
            $table->string('paid_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('gate_pass_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Generated, Used, Cancelled
            $table->timestamps();
        });

        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->string('gate_pass_no')->unique();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('gate_pass_status_id')->constrained('gate_pass_statuses');
            $table->string('customer_name');
            $table->string('customer_id_type')->nullable(); // TIN, ID, Passport
            $table->string('customer_id_no')->nullable();
            $table->dateTime('gate_pass_generated_date');
            $table->string('generated_by')->nullable();
            $table->dateTime('gate_pass_used_date')->nullable();
            $table->string('used_by')->nullable();
            $table->text('authorization_notes')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
            $table->index('gate_pass_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
        Schema::dropIfExists('gate_pass_statuses');
        Schema::dropIfExists('job_card_payments');
        Schema::dropIfExists('payment_modes');
        Schema::dropIfExists('payment_statuses');
    }
};
