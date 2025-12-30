<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = file_get_contents('database/seeds/words.english');
        $words = explode("\n", $words);

        $batchSize = 250;
        $totalItems = count($words);
        $data = [];
        for ($x = 0; $x <= ($totalItems - 1); ++$x) {
            // Chunk inserts into database, because shoving al at once, will choke the database-server,
            // and one at a time will take a lot of time.
            $data[] = $words[$x];
            if ($x > 0 && 0 === $x % $batchSize) {
                $sql = 'INSERT INTO word (word) VALUES ("'.implode('"),("', $data).'");';
                DB::insert($sql);
                $data = [];
            }
        }
    }
}
