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
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('pic_contact_id');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');

            // optional FK
            $table->foreign('province_id')
                  ->references('id')
                  ->on('provinces')
                  ->nullOnDelete();

            $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */

     public function down(): void
     {
         Schema::table('companies', function (Blueprint $table) {
             $table->dropForeign(['province_id']);
             $table->dropForeign(['city_id']);
             $table->dropColumn(['province_id', 'city_id']);
         });
     }
};
