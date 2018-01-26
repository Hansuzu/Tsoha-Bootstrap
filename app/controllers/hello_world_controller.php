<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  echo 'Tämä on etusivu :3!';
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      echo 'Hello World!';
    }
  }
