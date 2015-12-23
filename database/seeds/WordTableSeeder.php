<?php

use Illuminate\Database\Seeder;

class WordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $words = file_get_contents('database/seeds/words.english');
        $words = explode("\n", $words);

        $batchSize = 250;
        $totalItems = sizeof($words);
        $data = [];
        for ($x = 0; $x <= ($totalItems-1); $x++)
        {
            // Chunk inserts into database, because shoving al at once, will choke the database-server,
            // and one at a time will take a lot of time.
            $data[] = $words[$x];
            if ($x > 0 && $x % $batchSize === 0) {
                $sql = 'INSERT INTO word (word) VALUES ("' . implode('"),("', $data) . '");';
                DB::insert($sql);
                $data = [];
            }            
        }
    }
}
