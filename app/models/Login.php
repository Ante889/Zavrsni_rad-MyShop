<?php


class Login
{

    public static function createUser($parameters=[])
    {
        
        $connection = DB::getInstance();
        $sql = "INSERT INTO `users`(`name`, `lastname`, `password`,`role`, `email`, `register_time`)
        VALUES (:name,:lastname,:password,:role,:email,:register_time)";
        $connection->prepare($sql) -> execute($parameters);

    }

    public static function checkEmail($parametar)
    {
        $connection = DB::getInstance();
        $sql = "SELECT email FROM users WHERE email = :email";
        $result = $connection->prepare($sql); 
        $result ->execute($parametar); 
        return $result -> fetchAll();
    }

    public static function checkPassword($parameter)
    {
        $connection = DB::getInstance();
        $sql = "SELECT password FROM users WHERE email = :email";
        $result = $connection->prepare($sql); 
        $result ->execute($parameter); 
        return $result -> fetchAll();
    }

    public static function getRole($parameter)
    {
        $connection = DB::getInstance();
        $sql = "SELECT role FROM users WHERE email = :email";
        $result = $connection->prepare($sql); 
        $result ->execute($parameter); 
        return $result -> fetchAll();
    }

    public static function SetRemembermeToken($parameters)
    {
        $connection = DB::getInstance();
        $sql = "update `users` set rememberme_token =:rememberme_token
        where email = :email";
        $connection->prepare($sql) -> execute($parameters);
    }

    public static function GetRemembermeToken($parameter)
    {
        $connection = DB::getInstance();
        $sql = "SELECT email,role FROM users WHERE rememberme_token = :rememberme_token";
        $result = $connection->prepare($sql); 
        $result ->execute($parameter); 
        return $result -> fetchAll();
    }

    public static function Setreset_password_token($parameters)
    {
        $connection = DB::getInstance();
        $sql = "update `users` set reset_password_token =:reset_password_token
        where email = :email";
        $connection->prepare($sql) -> execute($parameters);
    }

    public static function UpdatePassword($parameters)
    {
        $connection = DB::getInstance();
        $sql = "update `users` set password = :password,reset_password_token =:reset_password_token2 where reset_password_token =:reset_password_token";
        $connection->prepare($sql) -> execute($parameters);
    }

}