<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('reporter_user_id');
            $table->unsignedBigInteger('assigned_user_id')->nullable();

            $table->string('ticket_number')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('answer')->nullable();

            $table->unsignedBigInteger('project_id')->nullable();

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'waiting-approval', 'approval-done','on-progress', 'customer-reply','pending','resolved','closed','reopened','rejected'])->default('open');
            $table->enum('type', ['support', 'complaint', 'question'])->default('support');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('reporter_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            // $table->foreign('status_id')->references('id')->on('ticket_statuses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
