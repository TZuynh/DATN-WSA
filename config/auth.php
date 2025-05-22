<?php return array (
  'defaults' => 
  array (
    'guard' => 'web',
    'passwords' => 'users',
  ),
  'guards' => 
  array (
    'web' => 
    array (
      'driver' => 'session',
      'provider' => 'users',
    ),
  ),
  'providers' => 
  array (
    'users' => 
    array (
      'driver' => 'eloquent',
      'model' => 'App\\Models\\TaiKhoan',
    ),
  ),
  'passwords' => 
  array (
    'users' => 
    array (
      'provider' => 'users',
      'table' => 'password_reset_tokens',
      'expire' => 60,
      'throttle' => 60,
    ),
  ),
  'password_timeout' => 10800,
  'max_attempts' => '2',
);