<?php
class ArticleVersion extends BaseModel{
    public $id, $article_id, $parent_id, $user_id, $time, $active, $contents;
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function findAllVersions($article_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion WHERE article_id=:article_id");
        $query->execute(array("article_id" => $article_id));
        $row=$query->fetchAll();
        $versions=array();
        foreach($rows as $row){
            $versions[]=new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["article_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]
            ));
        }
        return $versions;
    }
    
    public static function findActiveVersions($article_id){
        //Actually there should be only one active version but theoritically allowing any version to be active is easier to implement
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion WHERE article_id=:article_id AND active=b'1'");
        $query->execute(array("article_id" => $article_id));
        $rows=$query->fetchAll();
        $versions=array();
        foreach($rows as $row){
            $versions[]=new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["article_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]
            ));
        }
        return $versions;
    }
}



?>