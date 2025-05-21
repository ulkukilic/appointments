<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTypesTable extends Migration
{
    /**
     * user_types tablosunu oluşturur.
     */
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            // Primary key: unsigned INT auto-increment
            $table->increments('user_type_id');

            //  user_type_name: VARCHAR(50), benzersiz
            $table->string('user_type_name', 50)->unique();
        });
    }

    /**
     * user_types tablosunu siler.
     */
    public function down()
    {
        Schema::dropIfExists('user_types');
    }
}


class CreateUsersTable extends Migration
{
    /**
     * users tablosunu oluşturur.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            //  Birincil anahtar user_id: unsigned INT auto-increment
            $table->increments('user_uni_id');

            // full_name: VARCHAR(100), zorunlu
            $table->string('full_name', 100);

            // email: VARCHAR(255), benzersiz
            $table->string('email', 255)->unique();

            //  password: VARCHAR(255)
            $table->string('password', 255);

            //  user_type_id: unsigned INT, user_types tablosuna referans
            $table->unsignedInteger('user_type_id');

            //  phone_number: VARCHAR(20), nullable
            $table->string('phone_number', 20)->nullable();

            //  gender: ENUM('Female','Male','Other'), zorunlu
            $table->enum('gender', ['Female','Male','Other']);

            //  created_at: DATETIME, default CURRENT_TIMESTAMP
             $table->timestamps(); 
            // user_type_id üzerinde index ve foreign key
            $table->index('user_type_id');
            $table->foreign('user_type_id')
                  ->references('user_type_id')
                  ->on('user_types')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    /**
     * users tablosunu siler.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
