<?php
class ArticleVersion extends BaseModel{
    public $id, $article_id, $parent_id, $user_id, $time, $active, $contents;
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion");
        $query->execute();
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $articles[] = new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["aarticle_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]));
        }
        return $articles;
    }
    
    public static function findAllVersions($article_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion WHERE article_id=:article_id");
        $query->execute(array("article_id" => $article_id));
        $row=$query->fetchAll();
        $versions=array();
        foreach($rows as $row){
            $versions[]=new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["aarticle_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]));
        }
        return $versions;
    }
    
    public static function findActiveVersion($article_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion WHERE article_id=:article_id AND active=1");
        $query->execute(array("article_id" => $article_id));
        $row=$query->fetchAll();
        $versions=array();
        foreach($rows as $row){
            $versions[]=new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["aarticle_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]));
        }
        return $versions;
    }
}



?>