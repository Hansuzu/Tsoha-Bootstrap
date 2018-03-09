<?php
class Language extends BaseModel{
    public $id, $name, $shortcode;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators=array("validate_name", "validate_shortcode");
    }
    public function validate_name(){
        $errors=array();
        if ($this->name == "" || $this->name==null || strlen($this->name)<3) $errors[]="There must be at least 3 characters in the name of a language.";
        if (strlen($this->name)>50) $errors[]="There cannot be more than 50 characters in language name.";
        if (self::languageNameExists($this->name, $this->id)) $errors[]="Name is already in use";
        return $errors;
    }
    public function validate_shortcode(){
        $errors=array();
        if ($this->shortcode == "" || $this->shortcode==null || strlen($this->shortcode)<3) $errors[]="There must be at least 3 characters in the nshortcode of a language.";
        if (strlen($this->shortcode)>10) $errors[]="There cannot be more than 10 characters in the shortcode.";
        if (self::shortcodeExists($this->shortcode, $this->id)) $errors[]="Shortcode is already in use";
        return $errors;
    }
    
    public function saveAsNew(){
        $query=DB::connection()->prepare("INSERT INTO Language (name, shortcode) VALUES (:name, :shortcode) RETURNING id");
        $query->execute(array("name" => $this->name, "shortcode" => $this->shortcode));
        $row=$query->fetch();
        $this->id=$row["id"];
    }
    public function update(){
        $query=DB::connection()->prepare("UPDATE Language  SET name=:name, shortcode=:shortcode WHERE id=:id");
        $query->execute(array("name" => $this->name, "shortcode" => $this->shortcode, "id" => $this->id));
    }
    public function erase(){
        Article::removeAllWithLanguage($this->id);
        $query=DB::connection()->prepare("DELETE FROM Language WHERE id=:id");
        $query->execute(array("id" => $this->id));
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
    
    public static function shortcodeExists($shortcode, $ignore_id){
        $query=DB::connection()->prepare("SELECT * FROM Language WHERE shortcode=:shortcode AND id<>:id LIMIT 1");
        $query->execute(array("shortcode"=>$shortcode, "id"=>$ignore_id));
        if ($row=$query->fetch()) return true;
        return false;
    }
    public static function languageNameExists($name, $ignore_id){
        $query=DB::connection()->prepare("SELECT * FROM Language WHERE name=:name AND id<>:id LIMIT 1");
        $query->execute(array("name"=>$name, "id"=>$ignore_id));
        if ($row=$query->fetch()) return true;
        return false;
    }
}



?>