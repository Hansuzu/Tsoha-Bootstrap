<?php
class ArticleVersion extends BaseModel{
    public $id, $article_id, $parent_id, $user_id, $time, $active, $contents;
    public $print_contents;
    public $language;
    public $username;
    public $children;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->username="";
        $this->children=array();
        $this->validators=array("validate_article_id", "validate_user_id", "validate_contents");
    }
    
    //validators:
    public function validate_article_id(){
        $errors=array();
        if (Article::findById($this->article_id)==null) $errors[]="The article does not exist.";
        return $errors;
    }
    public function validate_user_id(){
        $errors=array();
        $person=Person::findById($this->user_id);
        if ($person==null) $errors[]="User does not exist.";
        else{
            $article=Article::findById($this->article_id);
            if ($article){
                if ($article->readonly==0x01 && $person->is_moderator==0x00){
                    $errors[]="Article is a readonly - article.";
                }else if ($person->edit_allowed==0x00){
                    $errors[]="The user has no rights to edit articles.";
                }
            }
        }
        return $errors;
    }
    public function validate_contents(){
        $errors=array();
        if ($this->contents == "" || $this->contents==null || strlen($this->contents)<3) $errors[]="There must be at least 3 characters in the contents of an article.";
        return $errors;    
    }
    
    
    
    public function language(){ //Return 
        $article=Article::findById($this->article_id);
        $language=null;
        if ($article!=null){
            $language=Language::findById($article->language_id);
        }
        return $language;
    }
    
    
    public function contents_motor(){
        //Creates $print_contents which contains html that is shown in article view page
        $language=$this->language();
        if ($language){
            $s=htmlspecialchars($this->contents);
            $s=preg_replace("/\n/", "<br>", $s);
            $s=preg_replace("/\/\*(.*?)\*\//", "", $s);
            $s=preg_replace("/\{\{(.*?)\}\}/", "", $s);
            $s=preg_replace("/\[h1\](.*?)\[\/h1\]/", "<h3>$1</h3>", $s);
            $s=preg_replace("/\[h2\](.*?)\[\/h2\]/", "<h4>$1</h3>", $s);
            $s=preg_replace("/\[b\](.*?)\[\/b\]/", "<b>$1</b>", $s);
            $s=preg_replace("/\[i\](.*?)\[\/i\]/", "<i>$1</i>", $s);
            $s=preg_replace("/\[s\](.*?)\[\/s\]/", "<s>$1</s>", $s);
            $s=preg_replace("/\[\[(.*?)\|(.*?)\]\]/", "<a href=\"".BASE_PATH."/page/view/".$language->shortcode."/$1\">$2</a>", $s);
            $s=preg_replace("/\[\[(.*?)\]\]/", "<a href=\"".BASE_PATH."/page/view/".$language->shortcode."/$1\">$1</a>", $s);
            $this->print_contents=$s;
        }else{
            $this->print_contents="QAQ";
        }
    }
    
    public function setAsActiveVersion(){
        //Set this ArticleVersion as an active version of this article (the version that is shown in the view page)
        //Also updates the superclass-pairs 
        
        //Set current active articleversion not active
        $query=DB::connection()->prepare("UPDATE ArticleVersion SET active=b'0' WHERE active=b'1' AND article_id=:article_id");
        $query->execute(array("article_id"=> $this->article_id)); 
        //Set this version active
        $query=DB::connection()->prepare("UPDATE ArticleVersion SET active=b'1' WHERE id=:id");
        $query->execute(array("id"=> $this->id));
        
        //Update superclass
        $s=preg_replace("/\/\*(.*?)\*\//", "", $this->contents); //First remove comments of contents
        preg_match_all("/\{\{(.*?)\}\}/", $s, $superclasses); //Find all superclasses 
        $superids=array();
        $language=$this->language();
        if ($language != null){
            foreach ($superclasses[1] as $superclass){
                $article=Article::findByNameAndLanguage($superclass, $language->id);
                foreach($article as $art){
                    $superids[]=$art->id;
                }
            }
        }
        ArticleSuperclass::eraseSupArticles($this->article_id);
        foreach ($superids as $superid){
            $newsuperpair=new ArticleSuperclass(array("suparticle_id" => $superid, "subarticle_id" => $this->article_id));
            $newsuperpair->save();
        }
    }
    
    public function saveNewVersion(){
        $query=DB::connection()->prepare("INSERT INTO ArticleVersion (article_id, parent_id, user_id, time, active, contents) 
                                                              VALUES (:article_id, :parent_id, :user_id, NOW(), b'0', :contents)
                                                              RETURNING id");
        $query->execute(array("article_id" => $this->article_id,
                              "parent_id" => $this->id,
                              "user_id" => $this->user_id,
                              "contents" => $this->contents));
        $row=$query->fetch();
        $this->id=$row["id"];
    }
    
    public static function getArticleId($version_id){
        $query=DB::connection()->prepare("SELECT article_id FROM ArticleVersion WHERE id=:version_id");
        $query->execute(array("version_id" => $version_id));
        if ($row=$query->fetch()){
            return $row["article_id"];
        }
        return -1;
    }
    
    public static function findAllVersions($article_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion WHERE article_id=:article_id");
        $query->execute(array("article_id" => $article_id));
        $rows=$query->fetchAll();
        $versions=array();
        foreach($rows as $row){
            $version=new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["article_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]
            ));
            $version->username=Person::getUsernameOfId($version->user_id);
            $versions[$version->id]=$version;
        }
        return $versions;
    }
    
    public static function findById($version_id){
        $query=DB::connection()->prepare("SELECT * FROM ArticleVersion WHERE id=:id");
        $query->execute(array("id" => $version_id));
        $row=$query->fetch();
        if ($row){
            return new ArticleVersion(array("id"=>$row["id"], 
                                        "article_id"=>$row["article_id"],
                                        "parent_id"=>$row["parent_id"],
                                        "user_id"=>$row["user_id"],
                                        "time"=>$row["time"],
                                        "active"=>$row["active"],
                                        "contents"=>$row["contents"]));
        }
        return null;
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