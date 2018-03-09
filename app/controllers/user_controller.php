<?php
class UserController extends BaseController{
    
    public static function viewUser($username, $update=false){
        $person=Person::findByUsername($username);
        $data=array("person"=>$person);
        if ($person){
            $user=self::get_user_logged_in();
            if ($user and $update){
                $ok=0;
                if ($user->isModerator()){
                    if (isset($_POST["edit_allowed"])) $person->edit_allowed=0x01;
                    else $person->edit_allowed=0x00;
                    if (isset($_POST["messages_allowed"])) $person->messages_allowed=0x01;
                    else $person->messages_allowed=0x00;
                    $ok=1;
                }
                if ($user->isAdmin()){
                    if (isset($_POST["is_admin"])) $person->is_admin=0x01;
                    else $person->is_admin=0x00;
                    if (isset($_POST["is_moderator"])) $person->is_moderator=0x01;
                    else $person->is_moderator=0x00;
                    $ok=1;
                }
                if ($ok){
                    $person->update();
                    $data["message"]="10-4";
                }
            }
        }
        View::make('user.html', $data);
    }
    public static function logout(){
        $_SESSION["user"] = null;
    }
    
    public static function logoutPage(){
        self::logout();
        $redirect_to="/login";
        if (isset($_POST["redirect_to"])) $redirect_to=$_POST["redirect_to"];
        Redirect::to($redirect_to, array("message" => "You've logged out!"));
    }
    
    public static function login($username, $password, $redirect_to){
        $user=Person::login($username, $password);
        if ($user){
            $_SESSION["user"] = $user->id;
            Redirect::to($redirect_to, array("message" => "You've logged in!"));
        }
        Redirect::to("/login", array("error_message" => "Could not log in.", "redirect_to"=>$redirect_to));
    }
    
    public static function loginPage(){
        if (isset($_POST["username"]) and isset($_POST["password"]) and isset($_POST["redirect_to"])){
            self::login($_POST["username"], $_POST["password"], $_POST["redirect_to"]);
        }
        $redirect_to="/login";
        if (isset($_POST["redirect_to"])){
            $redirect_to=$_POST["redirect_to"];
        }
        View::make("login.html", array("redirect_to"=>$redirect_to));
    }
    
    public static function signup($username, $email, $password){
        $user = new Person(array("username"=>$username, "email"=>$email));
        $user->setPassword($password);
        $errors=$user->errors();
        if (count($errors)==0){
            $user->saveAsNew();
            Redirect::to("/login", array("message" => "You've successfully signed up."));
        }
        return $errors;
    }
    
    public static function signupPage(){
        $data=array("username"=>"", "email"=>"");
        if (isset($_POST["username"]) and isset($_POST["email"]) and isset($_POST["password"]) and isset($_POST["password2"])){
            $data["username"]=$_POST["username"];
            $data["email"]=$_POST["email"];
            if ($_POST["password"]!=$_POST["password2"]){
                $data["error_message"]="Passwords do not match";
            }else{
                $errors=self::signup($_POST["username"], $_POST["email"], $_POST["password"]);
                if (count($errors)){
                    $data["error_message"]=$errors[0]; //View first error
                }
            }
        }
        View::make("signup.html", $data);
    }
}

?>