<?php


class Login
{

    public static function createUser($parameters=[])
    {
        
        $connection = DB::getInstance();
        $sql = "INSERT INTO `users`(`name`, `lastname`, `password`, `email`, `register_time`)
        VALUES (:name,:lastname,:password,:email,:register_time)";
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

    public static function checkPassword($parametar)
    {
        $connection = DB::getInstance();
        $sql = "SELECT password FROM users WHERE email = :email";
        $result = $connection->prepare($sql); 
        $result ->execute($parametar); 
        return $result -> fetchAll();
    }

    public static function getRole($parametar)
    {
        $connection = DB::getInstance();
        $sql = "SELECT role FROM users WHERE email = :email";
        $result = $connection->prepare($sql); 
        $result ->execute($parametar); 
        return $result -> fetchAll();
    }

}