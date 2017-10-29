<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username', 20)->comment('用户名');
            $table->string('email', 30)->comment('邮箱');
            $table->string('face')->default('')->comment('头像');
            $table->string('password', 100)->comment('密码');
            $table->string('sign', 100)->default('')->comment('个性签名');
            $table->string('web_url', 100)->default('')->comment('网站地址');
            $table->string('last_login_ip', 15)->default(0);
            $table->timestamp('last_login_time')->nullable();
            $table->tinyInteger('active')->default(0)->comment('激活(0|1)');
            $table->tinyInteger('status')->default(1)->comment('状态((0|1)');
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
        Schema::dropIfExists('users');
    }
}
