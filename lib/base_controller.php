<?php

  class BaseController{

    public static function get_user_logged_in(){
        if (isset($_SESSION["user"])){
            $user_id=$_SESSION["user"];
            $user=Person::findById($user_id);
            return $user;
        }
        return null;
    }

    public static function check_logged_in(){
        $logged_in=false;
        if (isset($_SESSION["user"])){
            if (!self::get_user_logged_in()){
                $_SESSION["user"]=null;
            }else{
                $logged_in=true;
            }
        }
        if (!$logged_in){
            Redirect::to("/login", array("message"=>"Log in to continue.", "redirect_to"=>$_SERVER["REQUEST_URI"]));
        }
    }
    

  }
