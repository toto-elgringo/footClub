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




// plus faire un autoload comme ca (cf init.php MVC framework php formation)
// y mettre ausi u fichier routes pour tout gerer et fiare un projet professionel

// require_once __DIR__ . "/../config/config.php";
// require_once __DIR__ . "/../config/database.php";
// spl_autoload_register(function ($class_name) {
//     $paths = [
//         __DIR__ . "/../app/controllers/",
//         __DIR__ . "/../app/models/",
//     ];

//     foreach ($paths as $path) {
//         $file = $path . $class_name . ".php";

//         if (file_exists($file)) {
//             require_once $file;
//             return;
//         }
//     }
// });