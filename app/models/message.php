<?php
class Message extends BaseModel{
    public $id, $article_id, $user_id, $message, $time, $edited;
    public $username;
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators=array("validate_message");
    }
    public function validate_message(){
        $errors=array();
        if (strlen($this->message)<3) $errors[]="There must be at least 3 characters in the message";
        return $errors;
    
    }
    
    public function saveAsNew(){
        $query=DB::connection()->prepare("INSERT INTO Message (article_id, user_id, message, time, edited) 
                                                 VALUES (:article_id, :user_id, :message, NOW(), NOW()) 
                                                 RETURNING id");
        $query->execute(array("article_id" => $this->article_id, "user_id" => $this->user_id, "message" => $this->message));
        $row=$query->fetch();
        $this->id=$row["id"];
    }

    public function update(){
        $query=DB::connection()->prepare("UPDATE Message  SET message=:message, edited=NOW() WHERE id=:id");
        $query->execute(array("message"=>$this->message, "id" => $this->id));
    }
    public function erase(){
        $query=DB::connection()->prepare("DELETE FROM Message WHERE id=:id");
        $query->execute(array("id" => $this->id));
    }
    
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM Message");
        $query->execute();
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $articles[] = new Message(array("id"=>$row["id"],
                                            "article_id"=>$row["article_id"],
                                            "user_id"=>$row["user_id"],
                                            "message"=>$row["message"],
                                            "time"=>$row["time"],
                                            "edited"=>$row["edited"]
            ));
        }
        return $articles;
    }
    
    public static function findAllInArticle($article_id){
        $query=DB::connection()->prepare("SELECT * FROM Message WHERE article_id=:article_id");
        $query->execute(array("article_id"=>$article_id));
        $rows=$query->fetchAll();
        $articles=array();
        foreach($rows as $row){
            $article= new Message(array("id"=>$row["id"],
                                            "article_id"=>$row["article_id"],
                                            "user_id"=>$row["user_id"],
                                            "message"=>$row["message"],
                                            "time"=>$row["time"],
                                            "edited"=>$row["edited"]
            ));
            $article->username=Person::getUsernameOfId($article->user_id);
            $articles[]=$article;
        }
        return $articles;
    }
    
    public static function findById($message_id){
        $query=DB::connection()->prepare("SELECT * FROM Message WHERE id=:message_id LIMIT 1");
        $query->execute(array("message_id"=>$message_id));
        $row=$query->fetch();
        if ($row){
            $article = new Message(array("id"=>$row["id"],
                                            "article_id"=>$row["article_id"],
                                            "user_id"=>$row["user_id"],
                                            "message"=>$row["message"],
                                            "time"=>$row["time"],
                                            "edited"=>$row["edited"]
            ));
            return $article; 
        }
        return null;
    }
}



?>