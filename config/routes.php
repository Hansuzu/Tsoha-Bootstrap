<?php

    $routes->get('/', function() {
        HelloWorldController::index();
    });
    
    $routes->get('/page', function() {
        ArticleController::index();
    });
    $routes->get('/page/:languageShortCode/:article_name', function($languageShortCode, $article_name) {
        ArticleController::viewArticle1($languageShortCode, $article_name);
    });
    $routes->get('/page/:languageShortCode/:article_name/:article_id', function($languageShortCode, $article_name, $article_id) {
        //Actually ignores everything but article_id, but the url is nice which is nice
        ArticleController::viewArticle2($languageShortCode, $article_name, $article_id);
    });
    
    $routes->get('/page/discussion', function() {
        HelloWorldController::discussionPage();
    });
    $routes->get('/page/edit', function() {
        HelloWorldController::editPage();
    });
    $routes->get('/user', function() {
        HelloWorldController::viewUser();
    });
    
    $routes->get('/user/:username', function($username) {
        UserController::viewUser($username);
    });
    
    $routes->get('/signup', function() {
        HelloWorldController::signup();
    });
    $routes->get('/login', function() {
        HelloWorldController::login();
    });
    

    $routes->get('/hiekkalaatikko', function() {
        HelloWorldController::sandbox();
    });
