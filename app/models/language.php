<?php
class Language extends BaseModel{
    public $id, $name;
    
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM Language");
        $query->execute();
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $articles[] = new Language(array( "id"=>$row["id"], "name"=>$row["name"]));
            ));
        }
        return $articles;
    }
    
    public static function find($id){
        $query=DB::connection()->prepare("SELECT * FROM Language WHERE id=:id");
        $query->execute(array("id"=>$id));
        $row=$query->fetch();
        if ($row){
            $language = new Language(array( "id"=>$row["id"], "name"=>$row["name"]));
            return $language;
        }
        return $null
    }
}



?>