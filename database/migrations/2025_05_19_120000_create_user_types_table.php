<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTypesTable extends Migration
{
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->increments('user_type_id');
            $table->string('user_type_name', 50)->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_types');
    }
}
