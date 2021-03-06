<?php
class AbstractArticle extends BaseModel{
    public $id;
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators=array();
    }
    
    public static function newAbstractArticle(){ //Creates new AbstractArticle to database and returns a corresponding instance of this class
        $query=DB::connection()->prepare("INSERT INTO AbstractArticle DEFAULT VALUES RETURNING id");
        $query->execute();
        $row=$query->fetch();
        return new AbstractArticle(array("id"=> $row["id"]));
    }
    
    public static function all(){ //Returns an array of all AbstractArticle's in database
        $query=DB::connection()->prepare("SELECT * FROM AbstractArticle");
        $query->execute();
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $articles[] = new AbstractArticle(array("id"=>$row["id"]));
        }
        return $articles;
    }
    
    public static function findById($id){ //Find a AbstractArticle with id $id and reeturn it. Return null if such does not exist.
        $query=DB::connection()->prepare("SELECT * FROM AbstractArticle WHERE id=:id LIMIT 1");
        $query->execute(array("id"=>$id));
        $row=$query->fetch();
        if ($row){
            $article = new AbstractArticle(array("id"=>$row["id"]));
            return $article;
        }
        return null; 
    }
}
?>