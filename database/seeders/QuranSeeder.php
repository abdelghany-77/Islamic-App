<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuranSurah;
use App\Models\QuranVerse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class QuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        QuranVerse::truncate();
        QuranSurah::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Downloading Quran data...');

        // Fetch Surah data
        $response = Http::get('https://api.alquran.cloud/v1/meta');
        if ($response->successful()) {
            $surahs = $response->json()['data']['surahs']['references'];

            // Create surah records
            foreach ($surahs as $surah) {
                QuranSurah::create([
                    'number' => $surah['number'],
                    'name_arabic' => $surah['name'],
                    'name_english' => $surah['englishName'],
                    'name_transliteration' => $surah['englishNameTranslation'],
                    'total_verses' => $surah['numberOfAyahs'],
                    'revelation_type' => $surah['revelationType'],
                ]);

                $this->command->info("Added Surah {$surah['englishName']}");
            }

            // Now fetch and add verses for each surah
            foreach ($surahs as $surah) {
                $surahNumber = $surah['number'];
                $this->command->info("Downloading verses for Surah {$surah['englishName']}...");

                // Get Arabic text
                $arabicResponse = Http::get("https://api.alquran.cloud/v1/surah/{$surahNumber}");

                // Get English translation
                $translationResponse = Http::get("https://api.alquran.cloud/v1/surah/{$surahNumber}/en.sahih");

                if ($arabicResponse->successful() && $translationResponse->successful()) {
                    $arabicVerses = $arabicResponse->json()['data']['ayahs'];
                    $translationVerses = $translationResponse->json()['data']['ayahs'];

                    // Add verses
                    for ($i = 0; $i < count($arabicVerses); $i++) {
                        QuranVerse::create([
                            'surah_id' => $surahNumber,
                            'verse_number' => $arabicVerses[$i]['numberInSurah'],
                            'arabic_text' => $arabicVerses[$i]['text'],
                            'translation' => $translationVerses[$i]['text'],
                            'transliteration' => null, // You can add this later if needed
                            'tafsir' => null, // You can add this later if needed
                        ]);
                    }

                    $this->command->info("Added {$surah['numberOfAyahs']} verses for Surah {$surah['englishName']}");
                }
            }

            $this->command->info('Quran data seeding completed successfully!');
        } else {
            $this->command->error('Failed to fetch Quran data from API.');
        }
    }
}
