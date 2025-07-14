<?php

function base_url($path = '') {

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];    
    $base_url = $protocol . $host . '/';  
    return $base_url . ltrim($path, '/');
}

function base_path($path = '') {
   $rootPath = dirname(__DIR__) ;
    return $rootPath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

function uploads_path($filename = '') {
  return base_path('uploads' . DIRECTORY_SEPARATOR . $filename);

} 

function uploads_URL($filename = '') {
  return base_path('uploads/' .ltrim($filename, '/') );

} 

function asset_url($path = '') {
  return base_url('assets/' . ltrim($path, '/'));
}

function redirect($url) {
    header("Location: " . base_url($url));
    exit();
}