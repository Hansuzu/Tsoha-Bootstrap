<?php

    $routes->get('/', function() {
        HelloWorldController::index();
    });
    
    $routes->get('/page', function() {
        ArticleController::index();
    });
    $routes->get('/page/view', function() {
        Redirect::to('/page');
    });
    
    $routes->get('/page/version/:version_id', function($version_id) {
        ArticleController::viewArticleByVersionId($version_id);
    });
    $routes->get('/page/view/:languageShortCode/:article_name', function($languageShortCode, $article_name) {
        ArticleController::viewArticleByNameAndLanguage($languageShortCode, $article_name);
    });
    $routes->get('/page/view/:languageShortCode/:article_name/:article_id', function($languageShortCode, $article_name, $article_id) {
        //Actually ignores everything but article_id, but the url is nice, which is nice
        ArticleController::viewArticleById($article_id);
    });
    $routes->get('/page/view/:languageShortCode/:article_name/:article_id/:version_id', function($languageShortCode, $article_name, $article_id, $version_id) {
        //Actually ignores everything but version_id, but the url is nice, which is nice
        ArticleController::viewArticleByVersionId($version_id);
    });
    
    $routes->get('/page/discussion/:languageShortCode/:article_name', function() {
        ArticleController::discussionPage();
    });
    $routes->get('/page/discussion/:languageShortCode/:article_name/:article_id', function() {
        ArticleController::discussionPage();
    });
    
    
    $routes->get('/page/edit/:version_id', function($version_id) {
        ArticleController::editArticle($version_id);
    });
    $routes->get('/page/edit', function() {
        Redirect::to('/page/new');
    });
    $routes->get('/page/new', function() {
        ArticleController::createArticle();
    });
    $routes->get('/page/new/:abstract_id', function($abstract_id) {
        ArticleController::lCreateArticle($abstract_id);
    });    
    
    $routes->get('/page/save', function() {
        ArticleController::saveArticle();
    });
    $routes->post('/page/save', function() {
        ArticleController::saveArticle();
    });
    
    
    
    $routes->get('/user/:username', function($username) {
        UserController::viewUser($username);
    });
    $routes->post('/user/:username', function($username) {
        UserController::viewUser($username, true);
    });
    
    
    $routes->get('/signup', function() {
        UserController::signupPage();
    });
    $routes->post('/signup', function() {
        UserController::signupPage();
    });
    
    $routes->get('/login', function() {
        UserController::loginPage();
    });
    $routes->post('/login', function() {
        UserController::loginPage();
    });
    $routes->get('/logout', function() {
        UserController::logoutPage();
    });
    $routes->post('/logout', function() {
        UserController::logoutPage();
    });
    
    $routes->get('/languages', function() {
        LanguageController::view();
    });
    $routes->post('/languages', function() {
        LanguageController::submit();
    });
    
    

    $routes->get('/hiekkalaatikko', function() {
        HelloWorldController::sandbox();
    });
