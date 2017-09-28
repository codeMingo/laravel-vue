<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dicts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code', 50)->comment('分类代码');
            $table->string('code_name', 50)->comment('分类名称');
            $table->tinyInteger('value')->unsigned()->comment('字典数值');
            $table->string('text_en', 50)->default('')->comment('字典英文文字');
            $table->string('text', 50)->default('')->comment('字典中文文字');
            $table->tinyInteger('sort_no')->unsigned()->default(0)->comment('排序号');
            $table->string('remarks', 50)->default('')->comment('备注');
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
        Schema::dropIfExists('dicts');
    }
}
