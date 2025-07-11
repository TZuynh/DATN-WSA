<?php return array (
  'driver' => 'file',
  'lifetime' => '30',
  'expire_on_close' => false,
  'encrypt' => false,
  'files' => storage_path('framework/sessions'),
  'connection' => NULL,
  'table' => 'sessions',
  'store' => NULL,
  'lottery' => 
  array (
    0 => 2,
    1 => 100,
  ),
  'cookie' => 'laravel_session',
  'path' => '/',
  'domain' => env('SESSION_DOMAIN', '.project.test'),
  'secure' => NULL,
  'http_only' => true,
  'same_site' => 'lax',
);