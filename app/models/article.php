<?php
class Article extends BaseModel{
    public $id, $abstract_id, $language_id, $readonly, $name;
    //No
    public $language;
    public $version;
    public $info;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators=array("validate_language", "validate_name");
    }
    public function validate_name(){
        $errors=array();
        if ($this->name == "" || $this->name==null || strlen($this->name)<3) $errors[]="There must be at least 3 characters in the name of an article.";
        if (strlen($this->name)>100) $errors[]="There cannot be more than 100 characters in the name of an article.";
        return $errors;
    }
    public function validate_language(){
        $errors=array();
        if (Language::findById($this->language_id)==null) $errors[]="The language does not exist.";
        return $errors;
    }
    
    public function isReadonly(){
        return $this->readonly==0x01;
    }
    public function saveAsNew(){
        $query=DB::connection()->prepare("INSERT INTO Article (abstract_id, language_id, readonly, name) 
                                                              VALUES (:abstract_id, :language_id, :readonly, :name)
                                                              RETURNING id");
        $query->execute(array("abstract_id" => $this->abstract_id, 
                              "language_id" => $this->language_id, 
                              "readonly" => $this->readonly,
                              "name" => $this->name));
        $row=$query->fetch();
        $this->id=$row["id"];
    }
    public function update(){
        $query=DB::connection()->prepare("UPDATE Article SET abstract_id=:abstract_id, language_id=:language_id, readonly=:readonly, name=:name 
                                                              WHERE id=:id");
        $query->execute(array("abstract_id" => $this->abstract_id, 
                              "language_id" => $this->language_id, 
                              "readonly" => $this->readonly,
                              "name" => $this->name,
                              "id" => $this->id));
    }
    
    public function loadLanguage(){
        $this->language=Language::findById($this->language_id);
    }
    public function loadVersion($version){
        $this->language=Language::findById($this->language_id);
        $version=ArticleVersion::findById($version);
        if (!$version){
            $this->info="A version of this article could not be found";
        }else{
            if ($version->article_id != $this->id){
                $this->info="The requested version is not a version of the requested article.";
            }else{
                $this->version=$version;
            }
        }
    }
    public function loadActiveVersion(){
        $versions=ArticleVersion::findActiveVersions($this->id);
        if (count($versions)==0){
            $this->info="There was no any version of this article on the database";
        }else if (count($versions)==1){
            $this->info="";
            $this->version=$versions[0];
        }else{
            $this->info="The version control of this article seems to be corrupt.";
            $this->version=$versions[0];
        }  
    }
    public function load($version=-1){ // Load language info and version
        self::loadLanguage();
        if ($version==-1) self::loadActiveVersion();
        else if ($version>=0) self::loadVersion($version);
    }
    public function remove(){
        $query=DB::connection()->prepare("SELECT id FROM Article WHERE id=:id");
        $query->execute(array("id"=>$this->id));
        if ($row=$query->fetch()){
            ArticleVersion::removeArticle($this->id);
            ArticleSuperclass::removeArticle($this->id);
            $query=DB::connection()->prepare("DELETE FROM Article WHERE id=:id");
            $query->execute(array("id"=>$this->id));
            return true;
        }
        return false;
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
                                            "name"=>$row["name"]
            ));
        }
        return $articles;
    }
    public static function test($a, $b){
        $a=preg_replace("/[^a-z]+/i", "", strtolower($a));
        $b=preg_replace("/[^a-z]+/i", "", strtolower($b));
        if (strlen($a)==0){
        }else if (strlen($b)==0){
            return true;
        }else if ((strpos($a, $b)) !== false){
            return true;
        }
        return false;
    }
    public static function search($terms){
        $articles=Article::all();
        $result=array();
        foreach($articles as $article){
            $article->loadActiveVersion();
            if (self::test($article->name, $terms)){
                $result[]=$article;
            }else if ($article->version){
                if (self::test($article->version->contents, $terms)){
                    $result[]=$article; 
                }
            }
        }
        return $result;
    }
    public static function findById($id){
        $query=DB::connection()->prepare("SELECT * FROM Article WHERE id=:id LIMIT 1");
        $query->execute(array("id" => $id));
        $row=$query->fetch();
        if ($row){
            $article=new Article(array("id"=>$row["id"], 
                                        "abstract_id"=>$row["abstract_id"],
                                        "language_id"=>$row["language_id"],
                                        "readonly"=>$row["readonly"],
                                        "name"=>$row["name"]
            ));
            return $article;
        }
        return null;
    }
    
    public static function findByNameAndLanguage($name, $language_id){
        $query=DB::connection()->prepare("SELECT * FROM Article WHERE UPPER(name) LIKE UPPER(:name) AND language_id=:language_id");
        $query->execute(array("name" => $name, "language_id"=>$language_id));
        $rows=$query->fetchAll();
        $articles=array();
        foreach ($rows as $row){
            $articles[]=new Article(array("id"=>$row["id"], 
                                        "abstract_id"=>$row["abstract_id"],
                                        "language_id"=>$row["language_id"],
                                        "readonly"=>$row["readonly"],
                                        "name"=>$row["name"]
            ));
        }
        return $articles;
    }
    
    public static function findByAbstractId($abstract_id){
        $query=DB::connection()->prepare("SELECT * FROM Article WHERE abstract_id=:abstract_id");
        $query->execute(array("abstract_id"=>$abstract_id));
        $rows=$query->fetchAll();
        $articles=array();
        foreach ($rows as $row){
            $articles[]=new Article(array("id"=>$row["id"],
                                        "abstract_id"=>$row["abstract_id"],
                                        "language_id"=>$row["language_id"],
                                        "readonly"=>$row["readonly"],
                                        "name"=>$row["name"]
            ));
        }
        return $articles;    
    }
    public static function removeAllWithLanguage($language_id){
        $query=DB::connection()->prepare("SELECT id FROM Article WHERE language_id=:language_id");
        $query->execute(array("language_id"=>$language_id));
        $rows=$query->fetchAll();
        $articles=array();
        foreach ($rows as $row){
            ArticleVersion::removeArticle($row["id"]);
            ArticleSuperclass::removeArticle($row["id"]);
        }
        $query=DB::connection()->prepare("DELETE FROM Article WHERE language_id=:language_id");
        $query->execute(array("language_id"=>$language_id));
        return $articles;  
    }
}


?>