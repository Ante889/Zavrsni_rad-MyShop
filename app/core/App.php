<?php

class App
{

    public static function start()
    {

        $Route = explode('/', Request::getRoute());

        //CLASS

        if(empty($Route[1])){
            $class = 'Index';
        }else{
            $class = ucfirst($Route[1]);
            unset($Route[0], $Route[1]);
        }
        $class .= 'Controller';

        //METHOD

        if(empty($Route[2])){
            $method = 'index';
        }else{
            $method = $Route[2];
            unset($Route[2]);
        }

        //INSTANCE

        if(class_exists($class) && method_exists($class, $method)){
            $instance = New $class();
            $instance -> $method($Route);
        }else{
            echo 'ne postoji ruta '. $class . '->' . $method;
        }

    }

}