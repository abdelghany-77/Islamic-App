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
        Schema::create('quran_surahs', function (Blueprint $table) {
        $table->id();
        $table->integer('number');
        $table->string('name_arabic');
        $table->string('name_english');
        $table->string('name_transliteration');
        $table->integer('total_verses');
        $table->string('revelation_type');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_surahs');
    }
};
