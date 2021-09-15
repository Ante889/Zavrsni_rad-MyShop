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

    public static function isLogin()
    {
        return isset($_SESSION['User']);
    }

    public static function isAdmin()
    {
        if(isset($_SESSION['User']->role)){
            return $_SESSION['User']->role === 'admin';
        }
    }

    public static function redirect($name)
    {
        header("Location:" . $name);
    }

    public static function issetTrim(string $string)
    {
        return isset($_POST[$string]) ? trim($_POST[$string]) : '';
    }

}