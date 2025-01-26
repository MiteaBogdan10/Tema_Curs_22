<?php

abstract class Base
{
    private $id;

    /**
     * @param $id
     */
    public function __construct($id=null)
    {
        if($id){
            $tableName = static::getTableName();
            $sql = "SELECT * FROM $tableName WHERE id = '$id' LIMIT 1;";
            $data = query($sql);

            $this->id = $id;
            if(count($data)>0){
                $this->fromArray($data[0]);
            }

        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



    public function fromArray($data)
    {
        foreach ($data as $attrName => $attrValue) {
            $this->$attrName = $attrValue;
        }
    }

    public function delete(){
        $id = $this->id;
        $tableName = $this->getTableName();
        global $mysql;

        $sql = "DELETE FROM $tableName WHERE id = $id;";
        $result = mysqli_query($mysql, $sql);

        if (!$result) {
            die("Eroare la ștergere: " . mysqli_error($mysql));
        }

        return true; // Întoarce true dacă ștergerea a avut succes
    }

    private function insert(){
        $tableName = $this->getTableName();
        $data = get_object_vars($this);
        unset($data['id']);
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            if(!is_null($value)){
            $columns[] = "`" . $key . "`";
            $values[] = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            }
        }
        $columnsStr = implode(', ', $columns);
        $valuesStr = implode(', ', $values);

        $sql = "INSERT INTO `$tableName` ($columnsStr) VALUES ($valuesStr);";

        global $mysql;
        $query = mysqli_query($mysql,$sql);

        if($query === false)
        {
            die('Error on:'.$sql);
        }

        $this->id =  mysqli_insert_id($mysql);
    }

    public function save()
    {
        if($this->id>0)
        {
            $this->update();
        }else
        {
            $this->insert();
        }
    }

    private function update (){
        $id = $this->id;
        $tableName = $this->getTableName();
        $data = get_object_vars($this);
        unset($data['id']);
        $sets = [];
        foreach ($data as $key => $value) {
            if(!is_null($value)) {
                $sets[] = "`" . $key . "`='$value'";
            }
        }

        $setsStr = implode(', ', $sets);

        $sql = "UPDATE  $tableName SET $setsStr WHERE  `id`=$id;";

        global $mysql;
        $query = mysqli_query($mysql,$sql);
        if($query === false)
        {
            die('Error on:'.$sql);
        }
    }

    public static function findBy($column, $columnValue){
        $tableName = static::getTableName();
        $sql = "SELECT * FROM $tableName WHERE $column = '$columnValue';";
        $data = query($sql);
        $objList=[];
        foreach ($data as $item){
            $objList[] = static::objFromArray($item);
        }

        return $objList;
    }

    public static function findOneBy($column, $columnValue){
        $tableName = static::getTableName();
        $sql = "SELECT * FROM $tableName WHERE $column = '$columnValue' LIMIT 1;";
        $result = query($sql);
        if(isset($result[0])){
            return static::objFromArray($result[0]);
        }else{
            return false;
        }
    }

    private static function objFromArray($array){
        $className = static::class;
        $object = new $className();
        $object->fromArray($array);
        return $object;
    }

    public static function find($id){
        $tableName = static::getTableName();
        $sql = "SELECT * FROM $tableName WHERE id = '$id' LIMIT 1;";
        $result = query($sql);
        if(isset($result[0])){
           return static::objFromArray($result[0]);
        }else{
            return false;
        }
    }

    public static function findAll(){
        $tableName = static::getTableName();
        global $mysql;
        $sql = "SELECT * FROM $tableName;";
        $query = mysqli_query($mysql,$sql);
        $data =  mysqli_fetch_all($query, MYSQLI_ASSOC);

        $objList=[];
        foreach ($data as $item){
            $objList[] = static::objFromArray($item);
        }

        return $objList;

    }

    public static abstract function getTableName();

}