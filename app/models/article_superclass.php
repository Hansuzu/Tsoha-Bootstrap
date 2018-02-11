<?php
class ArticleSuperclass extends BaseModel{
    public $subarticle_id, $suparticle_id;
    
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM ArticleSuperclass");
        $query->execute();
        $rows=$query->fetchAll();
        $superclasses=array();
        foreach ($rows as $row) {
            $superclasses[] = new ArticleSuperclass(array("subarticle_id"=>$row["subarticle_id"], "suparticle_id"=>$row["suparticle_id"]));
        }
        return $superclasses;
    }
    
    public static function findSupArticles($article_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleSuperclass WHERE suparticle_id=:suparticle_id");
        $query->execute(array("suparticle_id" => $suparticle_id));
        $rows=$query->fetchAll();
        $superclasses=array();
        foreach ($rows as $row) {
            $superclasses[] = new ArticleSuperclass(array("subarticle_id"=>$row["subarticle_id"], "suparticle_id"=>$row["suparticle_id"]));
        }
        return $superclasses;
    }
    
    public static function findSubArticles($article_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleSuperclass WHERE subarticle_id=:subarticle_id");
        $query->execute(array("subarticle_id" => $subarticle_id));
        $rows=$query->fetchAll();
        $superclasses=array();
        foreach ($rows as $row) {
            $superclasses[] = new ArticleSuperclass(array("subarticle_id"=>$row["subarticle_id"], "suparticle_id"=>$row["suparticle_id"]));
        }
        return $superclasses;
    }
    
    
}



?>