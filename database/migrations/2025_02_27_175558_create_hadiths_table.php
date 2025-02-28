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
        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('hadith_books');
            $table->string('reference_number');
            $table->text('arabic_text');
            $table->text('translation');
            $table->string('narrator');
            $table->string('authenticity')->nullable(); // sahih, hasan, etc.
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadiths');
    }
};
