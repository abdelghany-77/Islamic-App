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
        Schema::create('quran', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number');
            $table->string('surah_name_ar');
            $table->string('surah_name_en');
            $table->integer('ayah_number');
            $table->text('ayah_text');
            $table->text('translation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran');
    }
};
