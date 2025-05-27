<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('company_uni_id')->nullable()->after('user_type_id');
            $table->foreign('company_uni_id', 'fk_users_company')
                  ->references('company_uni_id')
                  ->on('companies')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_users_company');
            $table->dropColumn('company_uni_id');
        });
    }
}
