<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School Information
    |--------------------------------------------------------------------------
    */
    'school' => [
        'name' => env('SCHOOL_NAME', 'SMK Negeri 1'),
        'address' => env('SCHOOL_ADDRESS', ''),
        'logo' => env('SCHOOL_LOGO', '/images/logo.png'),
        'phone' => env('SCHOOL_PHONE', ''),
        'email' => env('SCHOOL_EMAIL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Academic Settings
    |--------------------------------------------------------------------------
    */
    'academic' => [
        'default_start_month' => 7, // Juli
        'default_end_month' => 6,   // Juni tahun berikutnya
        'weekend_days' => ['saturday', 'sunday'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Import Settings
    |--------------------------------------------------------------------------
    */
    'import' => [
        'max_rows' => 1000,
        'allowed_extensions' => ['xlsx', 'xls'],
        'max_file_size' => 2048, // KB
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    */
    'export' => [
        'pdf_orientation' => 'landscape', // landscape or portrait
        'include_logo' => true,
        'paper_size' => 'a4',
    ],

    /*
    |--------------------------------------------------------------------------
    | System Settings
    |--------------------------------------------------------------------------
    */
    'system' => [
        'session_timeout' => 120, // minutes
        'items_per_page' => 15,
        'date_format' => 'd/m/Y',
        'datetime_format' => 'd/m/Y H:i',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Type Colors (Default)
    |--------------------------------------------------------------------------
    */
    'colors' => [
        'MPLS' => '#10B981',      // green
        'PTS' => '#F59E0B',       // amber
        'PAS' => '#EF4444',       // red
        'PAT' => '#DC2626',       // dark red
        'ANBK' => '#8B5CF6',      // purple
        'LIBNAS' => '#6B7280',    // gray
        'LIBSEM' => '#3B82F6',    // blue
        'RAPAT' => '#14B8A6',     // teal
        'KEGIATAN' => '#EC4899',  // pink
    ],

];
