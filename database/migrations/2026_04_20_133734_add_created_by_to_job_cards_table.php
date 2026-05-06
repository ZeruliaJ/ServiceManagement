<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('job_cards', function (Blueprint $table) {
        $table->unsignedBigInteger('created_by')->nullable()->after('id');
    });
}

public function down()
{
    Schema::table('job_cards', function (Blueprint $table) {
        $table->dropColumn('created_by');
    });
}
};
