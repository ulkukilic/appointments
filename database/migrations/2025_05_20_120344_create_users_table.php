<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_uni_id');
            $table->string('full_name', 100);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->unsignedInteger('user_type_id');
            $table->string('phone_number', 20)->nullable();
            $table->enum('gender', ['Female','Male','Other']);
            $table->timestamps();

            $table->index('user_type_id');
            $table->foreign('user_type_id')
                  ->references('user_type_id')
                  ->on('user_types')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
