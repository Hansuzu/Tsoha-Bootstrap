<?php
class AbstractArticle extends BaseModel{
    public $id;
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM AbstractArticle");
        $query->execute();
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $articles[] = new AbstractArticle(array("id"=>$row["id"]);
        }
        return $articles;
    }
    public static function findById($id){
        $query=DB::connection()->prepare("SELECT * FROM AbstractArticle WHERE id=:id LIMIT 1");
        $query->execute(array("id"=>$id));
        $row=$query->fetch();
        if ($row){
            $article = new AbstractArticle(array("id"=>$row["id"]);
            return $article;
        
        }
        return null;    
    }
}
?>