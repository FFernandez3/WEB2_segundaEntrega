<?php
class UsuarioModel
{
    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db_mangas;charset=utf8', 'root', '');
    }

    function getUser($email)
    {
        $query = $this->db->prepare("SELECT * FROM usuario WHERE email=?");
        $query->execute([$email]);
        $user = $query->fetch(PDO::FETCH_OBJ);
        return $user;
    }





}