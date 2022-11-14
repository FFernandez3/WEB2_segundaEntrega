<?php
require_once './libs/Router.php';
require_once './app/controllers/manga-api.controller.php';
require_once './app/controllers/auth-api.controller.php';

// crea el router
$router = new Router();

// defina la tabla de ruteo: URL, metodo, controller, funcion a ejecutar
$router->addRoute('mangas', 'GET', 'MangaApiController', 'getMangas');
$router->addRoute('mangas/:ID', 'GET', 'MangaApiController', 'getManga');
$router->addRoute("auth/token", 'GET', 'AuthApiController', 'getToken');
$router->addRoute('mangas/:ID', 'DELETE', 'MangaApiController', 'deleteManga');
$router->addRoute('mangas', 'POST', 'MangaApiController', 'insertManga'); 
$router->addRoute('mangas/:ID', 'PUT', 'MangaApiController', 'editManga'); 
$router->setDefaultRoute('MangaApiController', 'pageNotFound');


// ejecuta la ruta (sea cual sea)
$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);