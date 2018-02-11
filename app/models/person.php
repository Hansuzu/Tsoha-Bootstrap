<?php
class Person extends BaseModel{
    public $id, $username, $email, $pword, $is_admin, $is_moderator, $edit_allowed, $messages_allowed, $created;
    
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM Person");
        $query->execute();
        $rows=$query->fetchAll();
        $persons=array();
        foreach($rows as $row){
            $persons[] = new Person(array( "id"=>$row["id"],
                                            "username"=>$row["username"],
                                            "pword"=>$row["pword"],
                                            "is_admin"=>$row["is_admin"],
                                            "is_moderator"=>$row["is_moderator"],
                                            "edit_allowed"=>$row["edit_allowed"],
                                            "messages_allowed"=>$row["messages_allowed"],
                                            "created"=>$row["created"]
            ));
        }
        return $persons;
    }
    
    public static function findById($id){
        $query=DB::connection()->prepare("SELECT* FROM Person WHERE id=:id LIMIT 1");
        $query->execute(array("id"=>$id));
        $row=$query->fetch();
        if ($row){
            $person = new Person(array( "id"=>$row["id"],
                                            "username"=>$row["username"],
                                            "pword"=>$row["pword"],
                                            "is_admin"=>$row["is_admin"],
                                            "is_moderator"=>$row["is_moderator"],
                                            "edit_allowed"=>$row["edit_allowed"],
                                            "messages_allowed"=>$row["messages_allowed"],
                                            "created"=>$row["created"]
            ));
            return $person;
        }
        return null;
    }
    
    public static function findByUsername($username){
        $query=DB::connection()->prepare("SELECT* FROM Person WHERE username=:username LIMIT 1");
        $query->execute(array("username"=>$username));
        $row=$query->fetch();
        if ($row){
            $person = new Person(array( "id"=>$row["id"],
                                            "username"=>$row["username"],
                                            "pword"=>$row["pword"],
                                            "is_admin"=>$row["is_admin"],
                                            "is_moderator"=>$row["is_moderator"],
                                            "edit_allowed"=>$row["edit_allowed"],
                                            "messages_allowed"=>$row["messages_allowed"],
                                            "created"=>$row["created"]
            ));
            return $person;
        }
        return null;
    }
}



?>