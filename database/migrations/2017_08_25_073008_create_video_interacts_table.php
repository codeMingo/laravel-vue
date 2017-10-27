<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoInteractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_interacts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment('用户id');
            $table->integer('video_list_id')->comment('视频id');
            $table->tinyInteger('like')->default(0)->comment('点赞');
            $table->tinyInteger('hate')->default(0)->comment('反对');
            $table->string('ip_address', 15)->default('')->comment('ip地址');
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
        Schema::dropIfExists('video_interacts');
    }
}
