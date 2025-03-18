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
        Schema::create('azkar', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // Morning, Evening, After Prayer, etc.
            $table->text('arabic_text');
            $table->text('transliteration');
            $table->text('english_translation');
            $table->integer('repeat_count')->default(1);
            $table->text('reference')->nullable(); // Quran or hadith reference
            $table->text('benefits')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('azkar');
    }
};
