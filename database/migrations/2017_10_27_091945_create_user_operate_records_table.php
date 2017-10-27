<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOperateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_operate_records', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment('用户id');
            $table->string('action')->default('')->comment('控制器/方法名');
            $table->text('params')->nullable()->comment('参数');
            $table->string('text')->default('')->comment('说明');
            $table->string('ip_address', 15)->default('')->comment('ip地址');
            $table->tinyInteger('status')->default(1)->comment('状态(0|1)');
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
        Schema::dropIfExists('user_operate_records');
    }
}
