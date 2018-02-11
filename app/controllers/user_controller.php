<?php
class UserController extends BaseController{
    public static function viewUser($username){
        $person=Person::findByUsername($username);
        View::make('user.html', array("person"=>$person));
    }
}

?>