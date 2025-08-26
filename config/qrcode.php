<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Backend
    |--------------------------------------------------------------------------
    |
    | This option controls the default backend that is used to generate QR codes.
    | You can change this option to use a different backend if needed.
    |
    | Supported: "imagick", "svg", "eps"
    |
    */

    'backend' => env('QR_CODE_BACKEND', 'svg'),

    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | This option controls the default size of generated QR codes.
    |
    */

    'size' => env('QR_CODE_SIZE', 300),

    /*
    |--------------------------------------------------------------------------
    | QR Code Margin
    |--------------------------------------------------------------------------
    |
    | This option controls the default margin around generated QR codes.
    |
    */

    'margin' => env('QR_CODE_MARGIN', 10),

    /*
    |--------------------------------------------------------------------------
    | QR Code Error Correction
    |--------------------------------------------------------------------------
    |
    | This option controls the default error correction level for QR codes.
    | Higher levels provide better error correction but increase QR code size.
    |
    | Supported: "L", "M", "Q", "H"
    |
    */

    'error_correction' => env('QR_CODE_ERROR_CORRECTION', 'H'),

    /*
    |--------------------------------------------------------------------------
    | QR Code Encoding
    |--------------------------------------------------------------------------
    |
    | This option controls the default encoding for QR codes.
    |
    | Supported: "UTF-8", "ISO-8859-1", "ISO-8859-2", "ISO-8859-3", "ISO-8859-4",
    |           "ISO-8859-5", "ISO-8859-6", "ISO-8859-7", "ISO-8859-8", "ISO-8859-9",
    |           "ISO-8859-10", "ISO-8859-13", "ISO-8859-14", "ISO-8859-15", "ISO-8859-16"
    |
    */

    'encoding' => env('QR_CODE_ENCODING', 'UTF-8'),
];
