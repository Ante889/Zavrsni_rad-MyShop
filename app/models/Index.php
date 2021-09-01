<?php


class Index
{

    public static function Read()
    {
        
        $connection = DB::getInstance();
        $sql = 'SELECT * FROM categories';
        $result= $connection->prepare($sql);
        $result -> execute();

        return $result -> fetchAll();
    }

}