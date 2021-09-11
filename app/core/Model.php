<?php 


class Model

{

    public function getParameters(){

        $parameters = [];
        foreach (static::$db_parameters as $key) {
                if(property_exists($this, $key) && !empty($this->$key)){
                $parameters[$key] = $this -> $key;
            }
        }
        return $parameters;

    }


    public function create()
    {
        unset($this -> where);
        $parameters = $this -> getParameters();
        $keys ='';$values='';
        foreach($parameters as $key => $value)
        {
            $keys =$key .','. $keys;
            $values=':'.$key.','.$values;
        }
        $connection = DB::getInstance();
        $sql = "INSERT INTO " . static::$db_table ." (" . rtrim($keys, ',') .") 
        VALUES (".rtrim($values, ',').")";
        $connection->prepare($sql) -> execute($parameters);

    }

    public function update (string $where)

    {
        $parameters = $this -> getParameters();
        $keys ='';
        $count = 0;
        foreach($parameters as $key => $value)
        {   $count ++;
            if(count($parameters) > $count){
            $keys =$keys.$key. '=:'.$key. ', ';
            }
        }
        $connection = DB::getInstance();
        $sql = "UPDATE ".static::$db_table. " set ".rtrim($keys, ' ,')." WHERE ".$where."=:where";
        $connection->prepare($sql) -> execute($parameters);
    }
    
   public function select (string $whereName)
   {
        $where = ['where' => $this -> where];
        $connection = DB::getInstance();
        $sql = "SELECT * FROM " .static::$db_table. " WHERE ".$whereName." = :where";
        $result = $connection -> prepare($sql);
        $result -> execute($where);
        return $result -> fetchALL();
   } 

   public function selectAll()
   {
        $connection = DB::getInstance();
        $sql = "SELECT * FROM " .static::$db_table;
        $result = $connection -> prepare($sql);
        $result -> execute();
        return $result -> fetchALL();

   }

   public function delete (string $where)
   {
        $where = ['where' => $this -> where];
        $connection = DB::getInstance();
        $result =$connection -> prepare("DELETE FROM ".static::$db_table." WHERE ". $where . "=:where");
        $result -> execute($where);
   }

}