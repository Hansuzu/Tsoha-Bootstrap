<?php

class HelloWorldController extends BaseController{

    public static function index(){
        View::make('home.html');
    }

    public static function page(){
        View::make('article.html');
    }
    public static function discussionPage(){
        View::make('article_discussion.html');
    }
    public static function editPage(){
        View::make('article_edit.html');
    }
    public static function viewUser(){
        View::make('user.html');
    }
    public static function signup(){
        View::make('signup.html');
    }
    public static function login(){
        View::make('login.html');
    }

    public static function sandbox(){
        // Testaa koodiasi täällä
        $p=Person::findByUsername("lol");
        Kint::dump($p);
    }
}
