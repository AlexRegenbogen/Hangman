<?php

declare(strict_types=1);

return [
    // Switch between database and API for word generation/selection
    //      1 - Use word-table in database,
    //      0 - will use API (random-word-api)
    'use_database' => (bool) env('USE_DATABASE_FOR_WORDS', true),
    'max_tries' => (int) env('MAX_TRIES', 6),
    'mask_char' => (string) env('MASK_CHAR', '.'),
];
