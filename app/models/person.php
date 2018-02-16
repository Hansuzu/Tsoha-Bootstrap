<?php
class Person extends BaseModel{
    public $id, $username, $email, $pword, $is_admin, $is_moderator, $edit_allowed, $messages_allowed, $created;
    
    private static function crypt_password($password){
        $password.="Zht-,]N#hR5kaabXu?hDe_ip}";
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function verify_password($password){
        $password.="Zht-,]N#hR5kaabXu?hDe_ip}";
        return password_verify($password, $this->pword);
    }
    
    
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators=array("validate_username", "validate_email");
    }
    
    
    
    public function validate_username(){
        $errors=array();
        if (strlen($this->username)<1) $errors[]="There must be more than 0 characters in the usernmae.";
        if (self::userWithUsernameExists($this->username)) $errors[]="Username is already taken.";
        return $errors;
    }
    public function validate_email(){
        $errors=array();
        if (!preg_match("/^[a-zA-Z0-9]+@[a-zA-Z0-9]\.[a-zA-Z0-9]$/", $this->email)) $errors[]="Invalid email.";
        if (self::userWithEmailExists($this->email)) $errors[]="There is already a user registered with this email.";
        return $errors;
    }
    
    
    public function editAllowed(){
        return $this->edit_allowed==0x01;
    }
    public function messagesAllowed(){
        return $this->messages_allowed==0x01;
    }
    public function isModerator(){
        return $this->is_moderator==0x01;
    }
    public function isAdmin(){
        return $this->is_admin==0x01;
    }
    
    public static function login($username, $password){
        $query=DB::connection()->prepare("SELECT * FROM Person WHERE username=:username");
        $query->execute(array($username));
        $row=$query->fetch();
        if ($row){
            $person=new Person(array("id"=>$row["id"],
                                    "username"=>$row["username"],
                                    "email"=>$row["email"],
                                    "pword"=>$row["pword"],
                                    "is_admin"=>$row["is_admin"],
                                    "is_moderator"=>$row["is_moderator"],
                                    "edit_allowed"=>$row["edit_allowed"],
                                    "messages_allowed"=>$row["messages_allowed"],
                                    "created"=>$row["created"]));
            if ($person->verify_password($password)){
                return $person;
            }
        }
        return null;
    
    }
    public function saveAsNew(){
        $query=DB::connection()->prepare("INSERT INTO Person (username, email, pword, is_admin, is_moderator, edit_allowed, messages_allowed, created)
                                                    VALUES (:username, :email, :pword, b'0', b'0', b'1', b'1', NOW())");
        $query->execute(array("username"=>$this->username, "email" => $this->email, "pword"=>$this->pword));
    }
    
    public function setPassword($newPassword){
        $this->pword=self::crypt_password($newPassword);
    }
    
    public function update(){
        $query=DB::connection()->prepare("UPDATE Person  SET    username=:username, email=:email, pword=:pword, is_admin=:is_admin, 
                                                                is_moderator=:is_moderator, edit_allowed=:edit_allowed,
                                                                messages_allowed=:messages_allowed
                                                        WHERE id=:id");
        $query->execute(array(  "username"=>$this->username, 
                                "email"=>$this->email,
                                "pword"=>$this->pword, 
                                "is_admin"=>$this->is_admin, 
                                "is_moderator"=>$this->is_moderator, 
                                "edit_allowed"=>$this->edit_allowed,
                                "messages_allowed"=>$this->messages_allowed,
                                "id"=>$this->id));
    }
    
    public static function all(){
        $query=DB::connection()->prepare("SELECT * FROM Person");
        $query->execute();
        $rows=$query->fetchAll();
        $persons=array();
        foreach($rows as $row){
            $persons[] = new Person(array( "id"=>$row["id"],
                                            "username"=>$row["username"],
                                            "email"=>$row["email"],
                                            "pword"=>$row["pword"],
                                            "is_admin"=>$row["is_admin"],
                                            "is_moderator"=>$row["is_moderator"],
                                            "edit_allowed"=>$row["edit_allowed"],
                                            "messages_allowed"=>$row["messages_allowed"],
                                            "created"=>$row["created"]));
        }
        return $persons;
    }
    
    public static function findById($id){
        $query=DB::connection()->prepare("SELECT* FROM Person WHERE id=:id LIMIT 1");
        $query->execute(array("id"=>$id));
        $row=$query->fetch();
        if ($row){
            return new Person(array( "id"=>$row["id"],
                                            "username"=>$row["username"],
                                            "email"=>$row["email"],
                                            "pword"=>$row["pword"],
                                            "is_admin"=>$row["is_admin"],
                                            "is_moderator"=>$row["is_moderator"],
                                            "edit_allowed"=>$row["edit_allowed"],
                                            "messages_allowed"=>$row["messages_allowed"],
                                            "created"=>$row["created"]));
        }
        return null;
    }
    
    public static function findByUsername($username){
        $query=DB::connection()->prepare("SELECT* FROM Person WHERE username=:username LIMIT 1");
        $query->execute(array("username"=>$username));
        $row=$query->fetch();
        if ($row){
            return new Person(array( "id"=>$row["id"],
                                            "username"=>$row["username"],
                                            "email"=>$row["email"],
                                            "pword"=>$row["pword"],
                                            "is_admin"=>$row["is_admin"],
                                            "is_moderator"=>$row["is_moderator"],
                                            "edit_allowed"=>$row["edit_allowed"],
                                            "messages_allowed"=>$row["messages_allowed"],
                                            "created"=>$row["created"]));
        }
        return null;
    }
    
    public static function userWithUsernameExists($username){
        $query=DB::connection()->prepare("SELECT * FROM Person WHERE username=:username LIMIT 1");
        $query->execute(array("username"=>$username));
        $row=$query->fetch();
        if ($row) return true;
        return false;
    }
    public static function userWithEmailExists($email){
        $query=DB::connection()->prepare("SELECT * FROM Person WHERE email=:email LIMIT 1");
        $query->execute(array("email"=>$email));
        $row=$query->fetch();
        if ($row) return true;
        return false;
    }
    public static function getUsernameOfId($id){
        $query=DB::connection()->prepare("SELECT username FROM Person WHERE id=:id LIMIT 1");
        $query->execute(array("id"=>$id));
        if ($row=$query->fetch()) return $row["username"];
        return "<user does not exist>";
    }
}



?>