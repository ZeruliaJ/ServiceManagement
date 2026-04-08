<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_code')->unique();
            $table->string('part_name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->string('unit_of_measure', 10);
            $table->boolean('is_active')->default(true);
            $table->text('specifications')->nullable();
            $table->timestamps();
        });

        Schema::create('part_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('quantity_on_hand');
            $table->integer('quantity_reserved');
            $table->integer('reorder_level');
            $table->integer('reorder_quantity');
            $table->dateTime('last_stock_check')->nullable();
            $table->timestamps();
            $table->unique(['part_id', 'warehouse_id']);
        });

        Schema::create('part_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->integer('quantity_reserved');
            $table->string('reservation_status'); // Reserved, Allocated, Consumed
            $table->dateTime('reservation_date');
            $table->dateTime('expected_fulfillment_date')->nullable();
            $table->dateTime('fulfillment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('charge_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Chargeable, Warranty, Goodwill, Campaign
            $table->timestamps();
        });

        Schema::create('job_card_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('parts');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);
            $table->foreignId('charge_type_id')->constrained('charge_types');
            $table->string('reason'); // Replacement, Adjustment, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_card_parts');
        Schema::dropIfExists('charge_types');
        Schema::dropIfExists('part_reservations');
        Schema::dropIfExists('part_stock');
        Schema::dropIfExists('parts');
        Schema::dropIfExists('warehouses');
    }
};
