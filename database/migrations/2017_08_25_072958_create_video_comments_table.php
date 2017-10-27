<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_id')->default(0)->comment('父id');
            $table->integer('video_list_id')->comment('视频id');
            $table->integer('user_id')->comment('用户id');
            $table->string('content')->comment('评论内容');
            $table->integer('is_audit')->default(0)->comment('审核(select)');
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
        Schema::dropIfExists('video_comments');
    }
}
