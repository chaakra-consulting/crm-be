<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('project_bukukas_id')->nullable();
            $table->unsignedBigInteger('pic_project_user_id')->nullable();
            $table->unsignedBigInteger('pic_company_user_id')->nullable();
            $table->integer('rewards')->nullable();
            $table->integer('feedback_point')->nullable();
            $table->string('feedback_text')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('pic_project_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('pic_company_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
