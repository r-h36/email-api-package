<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            // an email can use a template to send 
            $table->unsignedBigInteger('template_id')->nullable();
            $table->text('template_data')->nullable();
            // 0 means not using template, 1 means using template
            $table->tinyInteger('use_template')->default(0);

            // an email can also use plain content to send
            $table->text('plain_content')->nullable();

            $table->text('subject');

            // an email must have from email address and to email address
            $table->string('from');
            // to email address can be multiple
            $table->text('to');

            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->text('replyto')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('template_id')->references('id')->on('email_templates')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_logs');
    }
}
