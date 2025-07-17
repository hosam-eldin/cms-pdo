<?php

spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }else {
        die("Class $class_name not found in autoloader.");
    }
});