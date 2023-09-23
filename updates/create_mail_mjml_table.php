<?php namespace Waka\MailMjml\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateMailMjmlTable extends Migration
{
    public function up()
    {
        Schema::create('waka_mailmjml_mail_mjmls', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->integer('layout_id')->unsigned()->default(1);
            $table->string('subject');
            $table->text('mjml')->nullable();
            $table->mediumText('html')->nullable();
            $table->json('config');
            $table->integer('sort_order')->default(0);
            //softDelete
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_mailmjml_mail_mjmls');
    }
}