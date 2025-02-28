<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HadithBook;
use App\Models\Hadith;
use Illuminate\Support\Facades\DB;

class HadithSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Hadith::truncate();
        HadithBook::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Add hadith collections
        $books = [
            [
                'name' => 'Sahih Al-Bukhari',
                'author' => 'Imam Muhammad ibn Ismail al-Bukhari',
                'description' => 'Sahih al-Bukhari is a collection of hadith compiled by Imam Muhammad al-Bukhari. It is considered the most authentic collection of hadith and is one of the Kutub al-Sittah (six major hadith collections).'
            ],
            [
                'name' => 'Sahih Muslim',
                'author' => 'Imam Muslim ibn al-Hajjaj',
                'description' => 'Sahih Muslim is a collection of hadith compiled by Imam Muslim ibn al-Hajjaj. It is considered the second most authentic hadith collection after Sahih al-Bukhari.'
            ],
            [
                'name' => 'Sunan Abu Dawood',
                'author' => 'Abu Dawood Sulaiman ibn al-Ash\'ath',
                'description' => 'Sunan Abu Dawood is a collection of hadith compiled by Abu Dawood Sulaiman ibn al-Ash\'ath. It is one of the six major hadith collections (Kutub al-Sittah).'
            ],
            [
                'name' => 'Jami at-Tirmidhi',
                'author' => 'Abu Isa Muhammad ibn Isa at-Tirmidhi',
                'description' => 'Jami at-Tirmidhi is a collection of hadith compiled by Abu Isa Muhammad at-Tirmidhi. It is one of the six major hadith collections.'
            ],
            [
                'name' => 'Sunan an-Nasa\'i',
                'author' => 'Ahmad ibn Shu\'ayb an-Nasa\'i',
                'description' => 'Sunan an-Nasa\'i is a collection of hadith compiled by Ahmad ibn Shu\'ayb an-Nasa\'i. It is one of the six major hadith collections.'
            ],
            [
                'name' => 'Sunan Ibn Majah',
                'author' => 'Muhammad ibn Yazid Ibn Majah al-Qazvini',
                'description' => 'Sunan Ibn Majah is a collection of hadith compiled by Muhammad ibn Yazid Ibn Majah. It is one of the six major hadith collections (Kutub al-Sittah).'
            ],
        ];

        foreach ($books as $book) {
            HadithBook::create($book);
            $this->command->info("Added hadith collection: {$book['name']}");
        }

        // Add sample hadiths for each collection (you would get real data from an API or CSV)
        $sahihBukhari = HadithBook::where('name', 'Sahih Al-Bukhari')->first();

        $sampleHadiths = [
            [
                'book_id' => $sahihBukhari->id,
                'reference_number' => 'Hadith 1',
                'arabic_text' => 'إِنَّمَا الأَعْمَالُ بِالنِّيَّاتِ، وَإِنَّمَا لِكُلِّ امْرِئٍ مَا نَوَى',
                'translation' => 'The reward of deeds depends upon the intentions and every person will get the reward according to what he has intended.',
                'narrator' => 'Umar ibn Al-Khattab',
                'authenticity' => 'Sahih',
                'explanation' => 'This hadith emphasizes the importance of intentions in Islam. It teaches that Allah judges actions based on the intentions behind them, not just the outward appearance.'
            ],
            [
                'book_id' => $sahihBukhari->id,
                'reference_number' => 'Hadith 2',
                'arabic_text' => 'من كان يؤمن بالله واليوم الآخر فليقل خيرا أو ليصمت',
                'translation' => 'Whoever believes in Allah and the Last Day, should speak what is good or keep silent.',
                'narrator' => 'Abu Hurairah',
                'authenticity' => 'Sahih',
                'explanation' => 'This hadith teaches the importance of guarding one\'s tongue and speaking only what is beneficial. It encourages Muslims to avoid harmful, false, or pointless speech.'
            ],
            [
                'book_id' => $sahihBukhari->id,
                'reference_number' => 'Hadith 3',
                'arabic_text' => 'المسلم من سلم المسلمون من لسانه ويده',
                'translation' => 'A Muslim is the one from whose tongue and hands the Muslims are safe.',
                'narrator' => 'Abdullah ibn Amr',
                'authenticity' => 'Sahih',
                'explanation' => 'This hadith defines a true Muslim as someone who does not harm others with their speech or actions. It emphasizes the importance of peaceful and harmonious relationships among Muslims.'
            ],
        ];

        foreach ($sampleHadiths as $hadith) {
            Hadith::create($hadith);
        }

        $this->command->info('Added sample hadiths to Sahih Al-Bukhari collection');
        $this->command->info('Hadith data seeding completed successfully!');
        $this->command->info('Note: In a production environment, you would want to import a complete dataset of authentic hadiths.');
    }
}
