<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->tinyInteger('category_id')->comment('类别id');
            $table->string('title', 100)->comment('标题');
            $table->string('auther', 20)->comment('作者');
            $table->text('simple_instrution')->nullable()->comment('简介');
            $table->text('tag_ids')->nullable()->comment('标签');
            $table->string('source')->default('')->comment('来源');
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
        Schema::dropIfExists('videos');
    }
}
