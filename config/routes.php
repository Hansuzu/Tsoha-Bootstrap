<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });
  
  $routes->get('/page', function() {
    HelloWorldController::page();
  });
  $routes->get('/page/discussion', function() {
    HelloWorldController::discussionPage();
  });
  $routes->get('/page/edit', function() {
    HelloWorldController::editPage();
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
