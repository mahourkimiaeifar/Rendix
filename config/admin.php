<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix controls the secret URL segment for the Rendix admin panel.
    | Example:
    |
    | ADMIN_PREFIX=rx-nebula-7d31-k92m
    |
    | Then login URL becomes:
    | /rx-nebula-7d31-k92m/login
    |
    */

    'prefix' => trim((string) env('ADMIN_PREFIX', 'admin'), '/'),

    'name' => env('ADMIN_NAME', 'رندیکس'),

];