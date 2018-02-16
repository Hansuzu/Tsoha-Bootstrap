<?php
class ArticleSuperclass extends BaseModel{
    public $subarticle_id, $suparticle_id;
    public $subarticle, $suparticle;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators=array("validate_subarticle_id", "validate_suparticle_id");
    }
    
    //Validators:
    public function validate_subarticle_id(){
        $errors=array();
        if (Article::findById($subarticle_id)==null) $errors[]="Subarticle does not exist.";
        return $errors;
    }
    public function validate_suparticle_id(){
        $errors=array();
        if (Article::findById($suparticle_id)==null) $errors[]="Suparticle does not exist.";
        return $errors;
    }
    
    
    public function loadArticles(){ //Load the articles corresponding suparticle_id and subarticle_id
        $this->subarticle=Article::findById($this->subarticle_id);
        $this->suparticle=Article::findById($this->suparticle_id);
    }
    
    public function save(){
        $query=DB::connection()->prepare("INSERT INTO ArticleSuperclass (subarticle_id, suparticle_id) 
                                                 VALUES (:subarticle_id, :suparticle_id)");
        $query->execute(array("subarticle_id" => $this->subarticle_id, "suparticle_id" => $this->suparticle_id));
    }
    public function erase(){ //
        $query=DB::connection()->prepare("DELETE FROM ArticleSuperclass WHERE subarticle_id=:subarticle_id AND suparticle_id=:suparticle_id");
        $query->execute(array("subarticle_id" => $this->subarticle_id, "suparticle_id" => $this->suparticle_id));
    }
    public static function eraseSupArticles($article_id){
        $query=DB::connection()->prepare("DELETE FROM ArticleSuperclass WHERE subarticle_id=:subarticle_id");
        $query->execute(array("subarticle_id" => $article_id));        
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
    
    public static function findSubArticles($suparticle_id){ //Return all ArticleSuperclass's with superticle_id=$suparticle_id
        $query=DB::connection()->prepare("SELECT * FROM ArticleSuperclass WHERE suparticle_id=:suparticle_id");
        $query->execute(array("suparticle_id" => $suparticle_id));
        $rows=$query->fetchAll();
        $superclasses=array();
        foreach ($rows as $row) {
            $superclass = new ArticleSuperclass(array("subarticle_id"=>$row["subarticle_id"], "suparticle_id"=>$row["suparticle_id"]));
            $superclass->loadArticles();
            $superclasses[]=$superclass;
        }
        return $superclasses;
    }
    
    public static function findSupArticles($subarticle_id){ //Return all ArticleSuperclass's with suberticle_id=$subarticle_id
        $query=DB::connection()->prepare("SELECT * FROM ArticleSuperclass WHERE subarticle_id=:subarticle_id");
        $query->execute(array("subarticle_id" => $subarticle_id));
        $rows=$query->fetchAll();
        $superclasses=array();
        foreach ($rows as $row) {
            $superclass= new ArticleSuperclass(array("subarticle_id"=>$row["subarticle_id"], "suparticle_id"=>$row["suparticle_id"]));
            $superclass->loadArticles();
            $superclasses[]=$superclass;
        }
        return $superclasses;
    }
}



?>