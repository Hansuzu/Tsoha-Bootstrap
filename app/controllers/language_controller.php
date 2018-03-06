<?php
class LanguageController extends BaseController{

    public static function usercontrol(){
        self::check_logged_in();
        $user=self::get_user_logged_in();
        if (!$user->isModerator()){
            Redirect::to("", array("error_message"=>"Lol, you are not even a moderator :Dd"));
        }
    }
    
    public static function view($data=array()){
        self::usercontrol();
        $data["languages"]=Language::all();
        View::make("language_edit.html", $data);
    }
    
    
    public static function update($id, $name, $shortcode){
        $errors=array();
        $language=Language::findById($id);
        if ($language){
            $language->name=$name;
            $language->shortcode=$shortcode;
            $errors=array_merge($errors, $language->errors());
            if (count($errors)==0){
                $language->update();
            }
        }else $errors[]=array("The language to be edited does not exist.");
        return $errors;
    }
    public static function remove($id){
        $errors=array();
        $language=Language::findById($id);
        if ($language){
            $language->erase();
        }else $errors[]=array("The to-be-removed language does not exist (5/5 error message).");
        return $errors;
    }
    
    
    public static function submit(){
        self::usercontrol();
        $data=array();
        $errors=array();
        if (isset($_POST["edit"])){
            foreach($_POST["edit"] as $edit_id => $value){
                if (isset($_POST["name"][$edit_id]) and isset($_POST["shortcode"][$edit_id])){
                    $errors=array_merge($errors, self::update($edit_id, $_POST["name"][$edit_id], $_POST["shortcode"][$edit_id]));
                }else $errors[]="There is a mistake in the form (either a nasty bug or you are trying to do something you definitely should not).";
            }
        }
        if (isset($_POST["remove"])){
            foreach($_POST["remove"] as $remove_id => $value){
                $errors=array_merge($errors, self::remove($remove_id));                
            }
        }
        if (isset($_POST["new_name"]) and isset($_POST["new_shortcode"])){
            $language=new Language(array("name"=>$_POST["new_name"], "shortcode"=>$_POST["new_shortcode"], "id"=>-1));
            echo "QAQ";
            $errors_t=$language->errors();
            if (count($errors_t)==0){
                $language->saveAsNew();
            }$errors=array_merge($errors, $errors_t);
        }
        if (count($errors)){
            $errortxt="".count($errors)." errors. \n";
            foreach($errors as $error){
                $errortxt.=$error." \n";
            }
            $data["error_message"]=$errortxt;
        }
        self::view($data);
    }
}

?>