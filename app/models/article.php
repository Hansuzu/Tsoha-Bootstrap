<?php
class Article extends BaseModel{
    public $id, $abstract_id, $language_id, $readonly, $name;
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM Article");
        $query->execute();
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $articles[] = new Article(array("id"=>$row["id"], 
                                            "abstract_id"=>$row["abstract_id"],
                                            "language_id"=>$row["language_id"],
                                            "readonly"=>$row["readonly"],
                                            "name"=>$row["name"]);
        }
        return $articles;
    }
    public static function find($id){
    
        $query=DB::connection()->prepare("SELECT * FROM Article WHERE id=:id LIMIT 1");
        $query->execute(array("id" => $id));
        $row=$query->fetch();
        if ($row){
            $article=new Article(array("id"=>$row["id"], 
                                        "abstract_id"=>$row["abstract_id"],
                                        "language_id"=>$row["language_id"],
                                        "readonly"=>$row["readonly"],
                                        "name"=>$row["name"]));
            return $article;
        }
        return null;
    }
}



?>