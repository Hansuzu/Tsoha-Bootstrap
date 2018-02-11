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
            $articles[] = new Article(array("id"=>$row["id"]);
        }
        return $articles;
    }
}



?>