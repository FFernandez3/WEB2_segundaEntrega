<?php

class AuthApiHelper {
    function getToken(){ 
        $auth = $this->getAuthHeader(); // Bearer header.payload.signature
        $auth = explode(" ", $auth);
        if($auth[0]!="Bearer" || count($auth) != 2){
            return array();
        }
        $token = explode(".", $auth[1]);
        if(count($token)!=3){ //por si borro un punto del token 
            return array();
        }
        $header = $token[0];
        $payload = $token[1];
        $signature = $token[2];

        //firmo otra vez y veo que coincidan 
        $new_signature = hash_hmac('SHA256', "$header.$payload", "ClaveSecreta1234", true);
        $new_signature = base64url_encode($new_signature);
        if($signature!=$new_signature)
            return array();

        $payload = json_decode(base64_decode($payload)); //decodifico el payload, me queda un obj php
        if(!isset($payload->exp) || $payload->exp<time())
            return array();
        
        return $payload;
    }

    function isLoggedIn(){
        $payload = $this->getToken();
        if(isset($payload->id))
            return true;
        else
            return false;
    }

    function getAuthHeader(){
        $header = "";
        if(isset($_SERVER['HTTP_AUTHORIZATION'])){//para nginx u otros
            $header= $_SERVER['HTTP_AUTHORIZATION'];
            return $header;
        }
        if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])){ //esto lo pongo por apache, es el header q le puse en el htaccess
            $header= $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            return $header;
        }
        return null;
    }
}
