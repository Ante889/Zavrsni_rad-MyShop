<?php 


class globalModel
{

    public static function create($tableName,$parameters= [])
    {
        $keys ='';$values='';
        foreach($parameters as $key => $value)
        {
            $keys =$key .','. $keys;
            $values=':'.$key.','.$values;
        }
        $keys = rtrim($keys, ',');
        $values = rtrim($values, ',');

        $connection = DB::getInstance();
        $sql = "INSERT INTO " . $tableName ." (" . $keys .") 
        VALUES (".$values.")";
        $connection->prepare($sql) -> execute($parameters);
    }

    public static function update ($tableName,$parameters= [])
    {

        $keys ='';$wheres='';
        $count = 0;
        foreach($parameters as $key => $value)
        {   $count ++;
            if(count($parameters) > $count){
            $keys =$keys.$key. '=:'.$key. ', ';
            }
        }
        $connection = DB::getInstance();
        $sql = "UPDATE ". $tableName . " set ".$keys." WHERE id =:id";
        $connection->prepare($sql) -> execute($parameters);
        

    }

}