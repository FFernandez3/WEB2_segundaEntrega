<?php
require_once './app/models/manga.model.php';
require_once './app/views/api.view.php';


class MangaApiController
{
    private $view;
    private $model;
    private $data;


    function __construct()
    {
        $this->model = new MangaModel();
        $this->view = new ApiView();
        //obtengo los datos del get/post del body del request
        $this->data = file_get_contents("php://input");
    }

    private function getData()
    {
        return json_decode($this->data);
    }

    public function getMangas()
    {
        try {

            $input = $_GET;
            $page = $_GET['page'] ?? null;
            $search = $_GET['search'] ?? null;
            $sort = $_GET['sort'] ?? null;

            foreach ($input as $key => $value) {
                if ($key != 'page' && $key != 'sort' && $key != 'search' && $key != 'resource') {
                    //var_dump($key);
                    $this->view->response("Se ingresaron parametros incorrectos, ingrese sort, page, search o cualquier combinacion posible", 400);
                    die();
                }
            }

            //SI NO ESTA NINGUNO MUESTRO TODO
            if (!isset($search) && !isset($page) && !isset($sort)) {
                $this->getAllMangas();
                die();
            }

            //ORDENADO POR COLUMNA, PAGINADO Y CON FILTRADO
            else if (isset($search) && isset($page) && isset($sort)) {
                $this->getMangasOrderedPaginatedAndFiltered($sort, $page, $search);
                die();
            }
            //PAGINADO Y FILTRADO
            else if (isset($search) && isset($page) && !isset($sort)) {
                $this->getMangasPaginatedAndFiltered($search, $page);
                die();
            }
            //ORDENADO POR COLUMNA Y PAGINADO
            else if (isset($sort) && isset($page) && !isset($search)) {
                $this->getMangasOrderedAndPaginated($sort, $page);
                die();
            }
            //FILTRADO Y ORDENADO POR COLUMNA
            else if (isset($search) && isset($sort) && !isset($page)) {
                $this->getMangasOrderedAndFiltered($sort, $search);
                die();
            }
            //ORDENADO POR COLUMNA ASCENDENTEMENTE
            else if (isset($sort) && !isset($page) && !isset($search)) {
                $this->getMangasOrdered($sort);
                die();
            }

            //PAGINADO
            else if (isset($page) && !isset($sort) && !isset($search)) {
                $this->getMangasPaginated($page);
                die();
            }

            //FILTRADO
            if (isset($search) && !isset($sort) && !isset($page)) {
                $this->getMangasFiltered($search);
                die();
            }
        } catch (Exception $error) {
            $this->view->response("Error del servidor", 500);
        }
    }
   
    //Busca ordenado acendente
    function getAllMangas()
    {
        $mangas = $this->model->getAll();
        if ($mangas) {
            $this->view->response($mangas);
        } else {
            $this->view->response("No existen mangas", 404);
        }
    }
    //Ordenado, paginado y filtrado
    function getMangasOrderedPaginatedAndFiltered($sort, $page, $search)
    {
        if ($this->isAFieldOfTable($sort) && (is_numeric($page) && $page > 0) && ($search != null)) {
            $mangas = $this->model->getOrderedPaginatedAndFiltered($sort, $search, $page);
            if ($mangas) {
                $this->view->response($mangas);
            } else {
                $this->view->response("No es posible encontrar mangas segun $search en la pagina $page ordenados por $sort", 404);
            }
        } else {
            $this->showErrorParams();
        }
    }
    //paginado y filtrado
    function getMangasPaginatedAndFiltered($search = null, $page = null)
    {
        if (($search != null) && ($page != null) && (is_numeric($page) && $page > 0)) {
            $mangas = $this->model->getByTitlePaginated($search, $page);
            if ($mangas) {
                $this->view->response($mangas);
            } else {
                $this->view->response("No es posible encontrar mangas que incluyan la palabra $search en la pagina $page ", 404);
            }
        } else {
            $this->showErrorParams();
        }
    }
    //ordenado por columna y paginado
    function getMangasOrderedAndPaginated($sort = null, $page = null)
    {
        if ($this->isAFieldOfTable($sort) && $sort != null && $page != null && (is_numeric($page) && $page > 0)) {
            $mangas = $this->model->getOrderedAndPaginated($sort, $page);
            if ($mangas) {
                $this->view->response($mangas);
            } else {
                $this->view->response("No es posible encontrar mangas en la pagina $page ordenados por $sort", 404);
            }
        } else {
            $this->showErrorParams();
        }
    }
    //filtrado y ordenado por columna
    function getMangasOrderedAndFiltered($sort = null, $search = null)
    {
        if ($this->isAFieldOfTable($sort) && $sort != null && $search != null) {

            $mangas = $this->model->getOrderedAndFiltered($sort, $search);
            if ($mangas) {
                $this->view->response($mangas);
            } else {
                $this->view->response("No es posible encontrar mangas que contengan $search ordenados por $sort", 404);
            }
        } else {
            $this->showErrorParams();
        }
    }
    //ordenado segun sort
    function getMangasOrdered($sort = null)
    {
        if ($this->isAFieldOfTable($sort) && $sort != null) {
            $mangas = $this->model->getAllBySort($sort);
            if ($mangas) {
                $this->view->response($mangas);
            } else {
                $this->view->response("No fue posible encontrar mangas", 404); //si la tabla esta vacia
            }
        } else {
            $this->showErrorParam();
        }
    }
    //paginado
    function getMangasPaginated($page = null)
    {
        if (is_numeric($page) && $page > 0 && $page != null) {
            $quantity = $this->getQuantity();
            $limit = 2;
            //var_dump($quantity);
            if (($quantity / $limit >= $page) && ($quantity > 0)) {
                $mangas = $this->model->pagination($page);
                if ($mangas) {
                    $this->view->response($mangas);
                } else {
                    $this->view->response("No hay mangas en esta pagina", 404);
                }
            }
        } else {
            $this->showErrorParam();
        }
    }
    //filtrado
    function getMangasFiltered($search = null)
    {
        if ($search != null) {
            $mangas = $this->model->getByTitle($search);
            if ($mangas == null) {
                $this->view->response("No existen mangas con el titulo $search", 404);
            } else {
                $this->view->response($mangas);
            }
        } else {
            $this->showErrorParam();
        }
    }


    function getQuantity()
    {
        $quantity = $this->model->getRegistersQuantity(); //esto me trae un obj, no el int con la cantidad
        foreach ($quantity as $q) { //por eso hago este foreach y devuelvo el int
            return $q;
        }
    }
    
    // controla lo que se ingresa en el sort
    function isAFieldOfTable($sort)
    { 
        $fields = $this->model->getAllFieldsOfTable();
        //var_dump($fields);

        foreach ($fields as $field) {
            // var_dump($fields);

            foreach ($field as $fieldName) {
             
                if ($fieldName == $sort) {

                    return true;
                }
            }
        }
        return false;
    }


    function getManga($params = null)
    {
        if($params!=null)  {
            $id = $params[':ID'];
            if(is_numeric($id)&&$id>0){
                $manga = $this->model->get($id);

                // si no existe devuelvo 404
                if ($manga)
                    $this->view->response($manga);
                else
                    $this->view->response("El manga con el id=$id no existe", 404);
            }
            else{
                $this->view->response("El id ingresado es incorrecto, debe ser un numero mayor a 0", 400);
            }
           

        }      
       
    }

    public function deleteManga($params = null)
    { var_dump("entra al delete");
        if ($params!=null) {
            $id = $params[':ID'];
            if(is_numeric($id)&&$id>0){
                $manga = $this->model->get($id);
                if ($manga) {
                    $this->model->deleteById($id);
                    $this->view->response($manga); //respondo con el manga eliminado x si despues necesito hacer algo con el
                } else
                    $this->view->response("El manga con el id=$id no existe", 404);

            }
            else{
                $this->view->response("El valor del id  ingresado $id no es correcto, por favor ingrese un numero mayor a 0", 400);
            }
           
        } 
      
    }

    public function insertManga()
    {
        $manga = $this->getData();

        if (empty($manga->titulo) || empty($manga->autor) || empty($manga->sinopsis) || empty($manga->editorial) || empty($manga->id_genero_fk)) {
            $this->view->response("Complete los datos", 400);
        } else {
            $id = $this->model->insert($manga->titulo, $manga->autor, $manga->sinopsis, $manga->editorial, $manga->portada, $manga->id_genero_fk);
            if ($id != 0) {
                $manga = $this->model->get($id);
                $this->view->response($manga, 201);
            } else {
                $this->view->response("El manga no se pudo insertar", 400);
            }
        }
    }

    public function editManga($params = null)
    {
        
        $body = $this->getData(); //el body con lo q quiero cambiar
        
        
        if($params!=null){
            $id = $params[':ID'];
            
            if (is_numeric($id) && $id >= 0) {
                $manga = $this->model->get($id);
    
                if ($manga) {
                    $this->model->edit($body->titulo, $body->autor, $body->sinopsis, $body->editorial, $body->portada, $body->id_genero_fk, $id);
                    $this->view->response("La tarea se modifico con exito", 201);
                } else {
                    $this->view->response("La tarea con el id=$id no existe", 404);
                }
            }
            else{
                $this->view->response("El valor del id  ingresado $id no es correcto, por favor ingrese un numero mayor a 0", 400);
            }

        }
       
    }
    //FUNCIONES DE ERROR
 
    public function showErrorParams()
    {
        $this->view->response("Alguno de los parametros es erroneo o esta vacio", 400);
        die();
    }

    public function showErrorParam()
    {
        $this->view->response("El parametro es erroneo o esta vacio", 400);
        die();
    }
    public function pageNotFound() {
        $this->view->response("Page not found", 404);
        die();

    }
}
