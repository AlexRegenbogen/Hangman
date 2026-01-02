<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordTableSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $localesToImport = [
            'nl' => 'dutch',
            'en' => 'english',
        ];

        foreach ($localesToImport as $locale => $fileExtension) {
            $words = file_get_contents('database/seeds/words.'.$fileExtension);
            if (false === $words) {
                abort(500, 'Could not read words file');
            }
            $words = explode("\n", $words);

            $batchSize = 250;
            $totalItems = count($words);
            $data = [];
            for ($x = 0; $x <= ($totalItems - 1); ++$x) {
                // Chunk inserts into database, because shoving al at once, will choke the database-server,
                // and one at a time will take a lot of time.
                $data[] = $words[$x];
                if ($x > 0 && 0 === $x % $batchSize) {
                    $sql = 'INSERT INTO word (word, locale) VALUES ("'.implode('", "'.$locale.'"),("', $data).'", "'.$locale.'");';
                    DB::insert($sql);
                    $data = [];
                }
            }
        }

    }
}
