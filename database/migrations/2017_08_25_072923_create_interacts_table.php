<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInteractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interacts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment('用户id');
            $table->integer('article_id')->default(0)->comment('文章id');
            $table->integer('video_list_id')->default(0)->comment('视频id');
            $table->tinyInteger('like')->default(0)->comment('点赞');
            $table->tinyInteger('hate')->default(0)->comment('反对');
            $table->tinyInteger('collect')->default(0)->comment('收藏');
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
        Schema::dropIfExists('interacts');
    }
}
