<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('category_id')->comment('类别id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('title', 100)->comment('标题');
            $table->string('thumbnail')->default('')->comment('缩略图');
            $table->string('auther', 20)->default('')->comment('作者');
            $table->mediumText('content')->comment('内容');
            $table->string('tag_include')->default('')->comment('标签');
            $table->string('source')->default('')->comment('来源');
            $table->tinyInteger('recommend')->default(0)->comment('推荐(0|1)');
            $table->integer('is_audit')->default(0)->comment('审核(select)');
            $table->tinyInteger('status')->default(1)->comment('状态(select)');
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
        Schema::dropIfExists('articles');
    }
}
