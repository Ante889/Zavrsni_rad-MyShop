<?php

class DB
{

    private static $instance = null;


    private function __construct($base)
    {
        $dsn = 
        'mysql:host=' . $base['server'] . 
        ';dbname='. $base['name'];
        $pdo = new PDO($dsn, $base['user'], $base['password']);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
        if(!$pdo){
            echo "Database disconnected";
        }
    }

    public static function getInstance()
    {
        if(self::$instance == null){
            self::$instance = new self(App::config('database'));
        }
        return self::$instance;
    }
}

