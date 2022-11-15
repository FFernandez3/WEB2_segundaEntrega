<?php
require_once './app/models/manga.model.php';
require_once  './app/models/usuario.model.php';
require_once './app/views/api.view.php';
require_once './app/helpers/auth-api.helper.php';

//nuestra propia base64 para salvar el = del final que pone base64
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}


class AuthApiController {
    private $model;
    private $view;
    private $authHelper;

    private $data;

    public function __construct() {
       $this->model = new UsuarioModel();
        $this->view = new ApiView();
        $this->authHelper = new AuthApiHelper();
        
        // lee el body del request
        $this->data = file_get_contents("php://input");
    }

    private function getData() {
        return json_decode($this->data);
    }

    public function getToken($params = null) {
        // Obtener "Basic base64(user:pass)
        $basic = $this->authHelper->getAuthHeader();
        
        if(empty($basic)){
            $this->view->response('No autorizado', 401);
            return;
        }
        $basic = explode(" ",$basic); // ["Basic" "base64(user:pass)"]
        if($basic[0]!="Basic"){
            $this->view->response('La autenticación debe ser Basic', 401);
            return;
        }

        //validar usuario:contraseña
        $userpass = base64_decode($basic[1]); // user:pass
        $userpass = explode(":", $userpass);
        $userMail = $userpass[0];
        $pass = $userpass[1];

        $userDB= $this->model->getUser($userMail);
       // var_dump($userDB);
       //var_dump($userMail);
        //var_dump($userDB->email);
       // var_dump($pass);
        //var_dump($userDB->password);
        //var_dump(password_verify($pass, $userDB->password));

        //esto lo deberia obtener de la tabla usuarios no hardcodeado!!!
        if ($userDB && password_verify($pass, $userDB->password)&& ($userMail == $userDB->email)){
            
           // if($userMail == $userDB->email){
                //  crear un token
                $header = array(
                    'alg' => 'HS256',
                    'typ' => 'JWT'
                );
                $payload = array(
                    'id' => 1,
                    'name' => "Admin",
                    'exp' => time()+3600
                );
                //los paso a json y despues a base64
                $header = base64url_encode(json_encode($header));
                $payload = base64url_encode(json_encode($payload));
                $signature = hash_hmac('SHA256', "$header.$payload", "ClaveSecreta1234", true);
                $signature = base64url_encode($signature);
                $token = "$header.$payload.$signature";
                $this->view->response($token); //si el user y la contraseña estan bien devuelve el token
            // }else{
            //     $this->view->response('No autorizado', 401);
            // }
        }
        else{
            $this->view->response('No autorizado', 401);
        }
    }


}
