<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary URL (REQUIRED)
    |--------------------------------------------------------------------------
    | This key name is REQUIRED by cloudinary-labs/cloudinary-laravel v2.x
    | If this is missing or null → CloudinaryEngine::$url crashes
    */

    'cloudinary_url' => env('CLOUDINARY_URL'),

];
