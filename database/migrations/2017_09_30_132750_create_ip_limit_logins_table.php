<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpLimitLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_limit_logins', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('前后台(0|1)');
            $table->string('ip_address', 20)->comment('ip地址');
            $table->tinyInteger('status')->comment('状态(0|1)');
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
        Schema::dropIfExists('ip_limit_logins');
    }
}
