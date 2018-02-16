<?php

  class Redirect{

        public static function to($path, $message = null){
            // Katsotaan onko $message parametri asetettu
            if(!is_null($message)){
                // Jos on, lisätään se sessioksi JSON-muodossa
                $_SESSION['flash_message'] = json_encode($message);
            }
            
            if (strpos($path, BASE_PATH) === false) { //Jos BASE_PATH ei ole osa polkua, laitetaan se polun alkuun (5/5 hotfix)
                $path=BASE_PATH.$path;
            }
            // Ohjataan käyttäjä annettuun polkuun
            header('Location: ' . $path);

            exit();
        }

  }
