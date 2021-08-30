<?php

class Request
{

    public static function getRoute()
    {

        if(isset($_SERVER['REQUEST_URI'])){
            $Route = $_SERVER['REQUEST_URI'];
        }else if($_SERVER['REDIRECTION_PATH_INFO']){
            $Route = $_SERVER['REDIRECTION_PATH_INFO'];
        }
        return $Route;

    }

}