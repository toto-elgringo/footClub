<?php
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {

            $baseDir = __DIR__ . '/../';
            
            $file = $baseDir . str_replace('\\', '/', $class) . '.php';
            
            if (file_exists($file)) {
                require $file;
                return true;
            }
            
            $file = $baseDir . 'src/' . str_replace('\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            
            return false;
        });
    }
}

Autoloader::register();