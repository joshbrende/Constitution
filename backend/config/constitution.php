<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Amendment Bill No. 3 — canonical naming (align with official PDF title)
    |--------------------------------------------------------------------------
    |
    | Clause text in the database is maintained via the admin constitution
    | workflow. The PDF at storage path below is the optional official scan
    | or gazette copy for download; administrators replace it via upload.
    |
    */

    'amendment3_chapter_title' => env(
        'AMENDMENT3_CHAPTER_TITLE',
        'Constitution Amendment (No 3) Bill 2026'
    ),

    'amendment3_law_reference' => env(
        'AMENDMENT3_LAW_REFERENCE',
        'Constitution Amendment (No 3) Bill 2026'
    ),

    'amendment3_short_title_clause' => env(
        'AMENDMENT3_SHORT_TITLE',
        'This Act may be cited as the Constitution Amendment (No 3) Bill 2026.'
    ),

    'amendment3_official_pdf_disk' => 'public',

    'amendment3_official_pdf_path' => 'constitution-official/amendment3.pdf',

];
