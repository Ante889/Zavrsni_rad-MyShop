<?php 

session_start();


define('PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', __DIR__. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);


$path= implode(PATH_SEPARATOR,
[
    APP_PATH . 'models',
    APP_PATH . 'controller',
    APP_PATH . 'core'
]);

set_include_path($path);


spl_autoload_register(function($class){

    $path = explode(PATH_SEPARATOR, get_include_path());
    foreach ($path as $key) {
        if(file_exists($key. DIRECTORY_SEPARATOR. $class . '.php')){
            include $key. DIRECTORY_SEPARATOR. $class . '.php';
            break;
        }
    }

});

App::start();