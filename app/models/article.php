<?php
class Article extends BaseModel{
    public $id, $abstract_id, $language_id, $readonly, $name;
    //No
    public $language;
    public $version;
    public $info;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->language="lol";
    }
    public function load(){ // Load language info and version info
        $this->language=Language::findById($this->language_id);
        $versions=ArticleVersion::findActiveVersions($this->id);
        if (count($versions)==0){
            $this->info="Versiota tästä artikkelista ei löytynyt";
        }else if (count($versions)==1){
            $this->info="";
            $this->version=$versions[0];
        }else{
            $this->info="Tämän artikkelin versionhallinnassa on ongelmia.";
            $this->version=$versions[0];
        }
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
    
    public static function search($terms){
        $articles=Article::all();
        $result=array();
        foreach($articles as $article){
            $a=strtolower($article->name);
            $b=strtolower($terms);
            $a=preg_replace("/[^a-z]+/i", "", $a);
            $b=preg_replace("/[^a-z]+/i", "", $b);
            if ((strpos($a, $b)) !== false){
                $result[]=$article;
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
        $query=DB::connection()->prepare("SELECT * FROM Article WHERE name=:name AND language_id=:language_id");
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
}


?>