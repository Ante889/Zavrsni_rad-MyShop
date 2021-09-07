<?php 


class globalModel
{

    public static function create(string $tableName, array $parameters= [])
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

    public static function update (string $tableName, string $where,array $parameters= [])

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
    
   public static function select (string $tableName,array $select=[],string $whereName,array $where=[])

   {

        $selectString='';
        foreach($select as $key)
        {
            $selectString = $selectString.$key .",";
        }
        $connection = DB::getInstance();
        $sql = "SELECT " . rtrim($selectString, ' ,') . " FROM ". $tableName ." WHERE ".$whereName." = :where";
        $result = $connection -> prepare($sql);
        $result -> execute($where);
        return $result -> fetchALL();
   } 

   public static function delete (string $tableName, string $where, array $whereParm = [])

   {
        $connection = DB::getInstance();
        $result =$connection -> prepare("DELETE FROM ". $tableName ." WHERE ". $where . "=:where");
        $result -> execute();
   }

}