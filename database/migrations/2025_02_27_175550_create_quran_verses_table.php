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
        Schema::create('quran_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surah_id')->constrained('quran_surahs');
            $table->integer('verse_number');
            $table->text('arabic_text');
            $table->text('translation');
            $table->text('transliteration')->nullable();
            $table->text('tafsir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_verses');
    }
};
