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
        $connection = DB::getInstance();
        $sql = "INSERT INTO " . $tableName ." (" . rtrim($keys, ',') .") 
        VALUES (".rtrim($values, ',').")";
        $connection->prepare($sql) -> execute($parameters);
    }
    // Dodaj ime table-a, where parametar npr(id ili email) i zadnji parametar u array-u je where 
    
    public static function update ($tableName,$where,$parameters= [])
    {
        $keys ='';
        $count = 0;
        foreach($parameters as $key => $value)
        {   $count ++;
            if(count($parameters) > $count){
            $keys =$keys.$key. '=:'.$key. ', ';
            }
        }
        $connection = DB::getInstance();
        $sql = "UPDATE ". $tableName . " set ".rtrim($keys, ' ,')." WHERE ".$where."=:where";
        $connection->prepare($sql) -> execute($parameters);
        

    }

    //Ime tablice, što želiš povući , gdje, gdje parametar
   public static function select ($tableName, $what=[],$whereName, $where=[])
   {
        $whatString='';
        foreach($what as $key)
        {
            $whatString = $whatString.$key .",";
        }
        $connection = DB::getInstance();
        $sql = "SELECT " . rtrim($whatString, ' ,') . " FROM ". $tableName ." WHERE ".$whereName." = :where";
        $result = $connection -> prepare($sql);
        $result -> execute($where);
        return $result -> fetchALL();

   } 

   public static function delete ($tableName,$where,$whereParm)
   {

        $connection = DB::getInstance();
        $result =$connection -> prepare("DELETE FROM ". $tableName ." WHERE ". $where . "=". $whereParm);
        $result -> execute();

   }

}