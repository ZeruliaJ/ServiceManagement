<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
   public function up(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('warehouse')->nullable()->after('bill_to_party_id');
            $table->enum('odometer_working', ['yes', 'no'])->nullable()->after('odometer_in');
        });
    }

    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn(['warehouse_id', 'odometer_working']);
        });
    }
};
