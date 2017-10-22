<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username', 20)->comment('用户名');
            $table->string('email', 30)->comment('邮箱');
            $table->string('password', 100)->comment('密码');
            $table->integer('permission_id')->comment('权限id');
            $table->text('permission_include')->nullable()->comment('权限节点,空表示未自定义权限');
            $table->string('last_login_ip', 15)->default('')->comment('最后登录ip');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->tinyInteger('status')->default(1)->comment('状态(0|1)');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
