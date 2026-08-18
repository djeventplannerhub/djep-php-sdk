<?php
/**
 * DJEP PHP SDK - Standalone Autoloader
 *
 * If you're using Composer, you don't need this file.
 * Just use: require 'vendor/autoload.php';
 *
 * For manual installation:
 * require 'djep-php-sdk/autoload.php';
 */
spl_autoload_register(function ($class) {
    $prefix = 'DJEP\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
