<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyAvailabilityTable extends Migration
{
    public function up()
    {
        Schema::create('weekly_availability', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('staff_member_uni_id');
            $table->tinyInteger('weekday')->comment('0=Sunday,…,6=Saturday');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // foreign key: staff_members tablosuna bağla
            $table->foreign('staff_member_uni_id')
                  ->references('staff_member_uni_id')
                  ->on('staff_members')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_availability');
    }
}
