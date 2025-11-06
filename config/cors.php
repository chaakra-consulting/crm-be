<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | Tentukan route mana saja yang akan mengaktifkan CORS.
    | Biasanya hanya untuk route API dan sanctum.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Tentukan method HTTP mana yang diizinkan.
    | Gunakan ['*'] untuk mengizinkan semua (GET, POST, PUT, DELETE, dsb).
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Daftar domain frontend yang boleh akses API.
    | Saat production, ubah 'http://localhost:5173' ke domain FE kamu.
    |
    */

    'allowed_origins' => [
        env('APP_FRONTEND_URL', 'http://localhost:3000'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Bisa digunakan kalau kamu mau izin wildcard (misal subdomain).
    | Biasanya dikosongkan kecuali kamu butuh pattern tertentu.
    |
    */

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Header yang boleh dikirim ke API.
    | Gunakan ['*'] untuk menerima semua header.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Header yang boleh diekspos ke browser. Biasanya kosong kecuali butuh spesifik.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Waktu (detik) hasil preflight (OPTIONS) disimpan di browser.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Harus TRUE jika kamu pakai Laravel Sanctum + Vue (Axios withCredentials: true)
    |
    */

    'supports_credentials' => true,

];
