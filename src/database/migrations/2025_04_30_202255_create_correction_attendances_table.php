<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stamp_correction_request_id')->constrained()->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correction_attendances');
    }
}
