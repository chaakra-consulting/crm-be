<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_social_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->cascadeOnUpdate();
            $table->foreignId('social_media_id')->nullable()->constrained('social_medias')->cascadeOnUpdate();
            $table->string('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_social_medias');
    }
};
