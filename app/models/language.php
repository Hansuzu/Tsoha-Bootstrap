<?php
class Language extends BaseModel{
    public $id, $name, $shortcode;
    
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM Language");
        $query->execute();
        $rows=$query->fetchAll();
        $languages=array();
        foreach ($rows as $row) {
            $languages[] = new Language(array( "id"=>$row["id"], "name"=>$row["name"], "shortcode"=>$row["shortcode"]));
        }
        return $languages;
    }
    
    public static function findById($id){
        $query=DB::connection()->prepare("SELECT * FROM Language WHERE id=:id LIMIT 1");
        $query->execute(array("id"=>$id));
        $row=$query->fetch();
        if ($row){
            $language = new Language(array( "id"=>$row["id"], "name"=>$row["name"], "shortcode"=>$row["shortcode"]));
            return $language;
        }
        return null;
    }
    
    public static function findByName($name){
        $query=DB::connection()->prepare("SELECT * FROM Language WHERE name=:name LIMIT 1");
        $query->execute(array("name"=>$name));
        $row=$query->fetch();
        if ($row){
            $language = new Language(array( "id"=>$row["id"], "name"=>$row["name"], "shortcode"=>$row["shortcode"]));
            return $language;
        }
        return null;
    }
    public static function findByShortcode($shortcode){
        $query=DB::connection()->prepare("SELECT * FROM Language WHERE shortcode=:shortcode LIMIT 1");
        $query->execute(array("shortcode"=>$shortcode));
        $row=$query->fetch();
        if ($row){
            $language = new Language(array( "id"=>$row["id"], "name"=>$row["name"], "shortcode"=>$row["shortcode"]));
            return $language;
        }
        return null;
    }
}



?>