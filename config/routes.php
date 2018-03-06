<?php

    $routes->get('/', function() {
        HelloWorldController::index();
    });
    
    //Artikkelisivujen reitit:
    $routes->get('/page', function() { //Hakusivu
        ArticleController::index();
    });
    $routes->get('/page/view', function() { //Ohjaa view-sivu ilman tietoja hakusivulle
        Redirect::to('/page');
    });
    
    
    
    //Näytä artikkelista aktiivinen versio
    $routes->get('/page/view/:languageShortCode/:article_name', function($languageShortCode, $article_name) {
        ArticleController::viewArticleByNameAndLanguage($languageShortCode, $article_name);
    });
    //Näytä artikkelista aktiivinen versio (edelliseen verrattuna artikkeli-id:tä on täsmennetty. Tätä tarvitaan käytännössä, jos artikkelitietokannassa on jotain mennyt hupsusti)
    $routes->get('/page/view/:languageShortCode/:article_name/:article_id', function($languageShortCode, $article_name, $article_id) {
        //Actually ignores everything but article_id, but the url is nice, which is nice
        ArticleController::viewArticleById($article_id);
    });
    //Näytä artikkelista tietty versio 
    $routes->get('/page/view/:languageShortCode/:article_name/:article_id/:version_id', function($languageShortCode, $article_name, $article_id, $version_id) {
        //Actually ignores everything but version_id, but the url is nice, which is nice
        ArticleController::viewArticleByVersionId($version_id);
    });
     //Näytä artikkelista tietty versio, lyhyt osoite
    $routes->get('/page/version/:version_id', function($version_id) {
        ArticleController::viewArticleByVersionId($version_id);
    });
    
    
    //Artikkelien keskustelusivut
    $routes->get('/page/discussion/:languageShortCode/:article_name', function($languageShortCode, $article_name) {
        ArticleController::viewDiscussionByNameAndLanguage($languageShortCode, $article_name);
    });
    $routes->post('/page/discussion/:languageShortCode/:article_name', function($languageShortCode, $article_name) {
        ArticleController::viewDiscussionByNameAndLanguage($languageShortCode, $article_name);
    });
    $routes->get('/page/discussion/:languageShortCode/:article_name/:article_id', function($languageShortCode, $article_name, $article_id) {
        ArticleController::viewDiscussionById($article_id);
    });
    $routes->post('/page/discussion/:languageShortCode/:article_name/:article_id', function($languageShortCode, $article_name, $article_id) {
        ArticleController::viewDiscussionById($article_id);
    });
    
    //Historiasivut
    $routes->get('/page/history/:languageShortCode/:article_name', function($languageShortCode, $article_name) {
        ArticleController::viewHistoryByNameAndLanguage($languageShortCode, $article_name);
    });
    $routes->get('/page/history/:languageShortCode/:article_name/:article_id', function($languageShortCode, $article_name, $article_id) {
        ArticleController::viewHistoryById($article_id);
    });
    
    
    //Artikkelin muokkaussivut
    $routes->get('/page/edit/:version_id', function($version_id) {
        ArticleController::editArticle($version_id);
    });
    $routes->get('/page/edit', function() {
        Redirect::to('/page/new');
    });
    $routes->get('/page/new', function() { //Uusi artikkeli
        ArticleController::createArticle();
    });
    $routes->get('/page/new/:abstract_id', function($abstract_id) { //Uusi artikkeli tietystä aiheesta
        ArticleController::createArticleAboutExistingTopic($abstract_id);
    });    
    //Tallennus (kaikki edellisistä kutsuvat tätä)
    $routes->post('/page/save', function() { 
        ArticleController::saveArticle();
    });
    //Moderaattoritoiminnot... (aseta aktiiviseksi, readonly, jne...)
    $routes->get('/page/save', function() { 
        ArticleController::saveArticleBits();
    });
    
    
    
    
    
    //Käyttäjännäyttämissivu (jossa moderaattorit ja adminit voivat vaihtaa käyttäjän oikeuksia)
    $routes->get('/user/:username', function($username) {
        UserController::viewUser($username);
    });
    $routes->post('/user/:username', function($username) {
        UserController::viewUser($username, true);
    });
    
    
    
    
    //Rekisteröityminen
    $routes->get('/signup', function() {
        UserController::signupPage();
    });
    $routes->post('/signup', function() {
        UserController::signupPage();
    });
    
    
    
    //Kirjautuminen
    $routes->get('/login', function() {
        UserController::loginPage();
    });
    $routes->post('/login', function() {
        UserController::loginPage();
    });
    
    
    //Uloskirjautuminen
    $routes->get('/logout', function() {
        UserController::logoutPage();
    });
    $routes->post('/logout', function() {
        UserController::logoutPage();
    });
    
    
    
    //Kielisivu (moderaattorit ja admin)
    $routes->get('/languages', function() {
        LanguageController::view();
    });
    $routes->post('/languages', function() {
        LanguageController::submit();
    });
    
    

    
    $routes->get('/hiekkalaatikko', function() {
        HelloWorldController::sandbox();
    });
