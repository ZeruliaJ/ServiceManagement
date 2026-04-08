<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name'); // Active, Expired, Not Available
            $table->timestamps();
        });

        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->dateTime('warranty_start_date');
            $table->dateTime('warranty_end_date');
            $table->foreignId('warranty_status_id')->constrained('warranty_statuses');
            $table->integer('kilometers_limit')->nullable();
            $table->string('coverage_type')->nullable(); // Free Service, Parts, Labour, etc.
            $table->text('terms_conditions')->nullable();
            $table->string('source_system')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('warranty_statuses');
    }
};
