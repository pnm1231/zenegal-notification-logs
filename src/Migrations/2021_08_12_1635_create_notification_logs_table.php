<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36)->unique();
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->enum('medium', ['email', 'sms']);
            $table->string('subject')->nullable();
            $table->text('recipients')->nullable();
            $table->string('mailable_name')->nullable();
            $table->text('mailable')->nullable();
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->nullable();
            $table->index(['model', 'model_id']);
            $table->boolean('is_queued');
            $table->boolean('is_notification')->default(false);
            $table->text('notifiable')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('tries')->default(0);
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
        Schema::dropIfExists('notification_logs');
    }
}
