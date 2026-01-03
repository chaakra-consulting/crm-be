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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            // $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->cascadeOnUpdate();
            // $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('source_id')->nullable()->constrained('sources')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            // $table->foreignId('tags_id')->nullable()->constrained('tags')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('title_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number_1')->nullable();
            $table->string('phone_number_2')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
