<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_id')->default(0)->comment('父id');
            $table->integer('article_id')->comment('文章id');
            $table->integer('user_id')->comment('用户id');
            $table->text('content')->comment('评论内容');
            $table->integer('is_audit')->default(0)->comment('审核(select)');
            $table->tinyInteger('status')->default(1)->comment('状态(0|1)');
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
        Schema::dropIfExists('article_comments');
    }
}
