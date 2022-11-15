<?php
class MangaModel
{
    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db_mangas;charset=utf8', 'root', '');
    }
    //ORDENADO ASCENDENTE
    function getAll()
    {

        $query = $this->db->prepare("SELECT * FROM manga");


        $query->execute();
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);

        return $mangas;
    }

    function deleteById($id)
    {

        $query = $this->db->prepare('DELETE FROM manga WHERE id = ?');
        $query->execute([$id]);
    }

    function insert($title, $author, $synopsis, $publishingHouse, $coverPage = null, $genre)
    {
        $pathImg = null;
        if ($coverPage)
            $pathImg = $this->uploadImage($coverPage);


        $query = $this->db->prepare("INSERT INTO manga (titulo, autor, sinopsis, editorial, portada, id_genero_fk) VALUES (?, ?, ?, ?, ?, ?)");
        $query->execute([$title, $author, $synopsis, $publishingHouse, $pathImg, $genre]);

        return $this->db->lastInsertId();
    }

    function get($id)
    {
        //pido un manga
        $query = $this->db->prepare("SELECT manga.*, genero.nombre, genero.id_genero FROM manga INNER JOIN genero ON manga.id_genero_fk=genero.id_genero WHERE manga.id=$id");
        $query->execute();


        $manga = $query->fetch(PDO::FETCH_OBJ);

        return $manga;
    }

    function edit($title, $author, $synopsis, $publishingHouse, $coverPage = null, $genre, $id)
    {
        $query = $this->db->prepare('UPDATE manga  SET titulo=?, autor=?, sinopsis=?, editorial=?, portada=?, id_genero_fk=? WHERE id = ?');

        if ($coverPage) {
            $pathImg = $this->uploadImage($coverPage);

            $query->execute([$title, $author, $synopsis, $publishingHouse, $pathImg, $genre, $id]);
        } else {
            $query->execute([$title, $author, $synopsis, $publishingHouse, $coverPage, $genre, $id]);
        }

        $manga = $query->fetch(PDO::FETCH_OBJ);

        return $manga;
    }
    private function uploadImage($coverPage)
    {
        $target = 'images/' . uniqid() . '.jpg';
        move_uploaded_file($coverPage, $target);
        return $target;
    }

    //PAGINADO, FILTRADO Y ORDENADO
    function getOrderedPaginatedAndFiltered($sort, $search, $page)
    {
        $limit = 2;
        $offset = $page * $limit - $limit;
        $query = $this->db->prepare("SELECT * FROM manga  WHERE titulo LIKE ? ORDER BY $sort LIMIT $limit OFFSET $offset");
        $query->execute(["%$search%"]);
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
        return $mangas;
    }
    //FILTRADO Y PAGINADO
    function getByTitlePaginated($search, $page)
    {
        $limit = 2;
        $offset = $page * $limit - $limit;
        $query = $this->db->prepare("SELECT * FROM manga WHERE titulo LIKE ? LIMIT $limit OFFSET $offset");
        $query->execute(["%$search%"]);
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
        return $mangas;
    }

    //PAGINADO Y ORDENADO
    function getOrderedAndPaginated($sort, $page)
    {
        $limit = 2;
        $offset = $page * $limit - $limit;
        $query = $this->db->prepare("SELECT * FROM manga ORDER BY $sort LIMIT $limit OFFSET $offset ");
        $query->execute();
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
        return $mangas;
    }
    //FILTRADO Y ORDENADO
    function getOrderedAndFiltered($sort = null, $search)
    {
        $query = $this->db->prepare("SELECT * FROM manga   WHERE titulo LIKE ? ORDER BY $sort");

        $query->execute(["%$search%"]);
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
        return $mangas;
    }
    //ORDENADO POR UN CAMPO
    function getAllBySort($sort)
    {
        $query = $this->db->prepare("SELECT * FROM manga ORDER BY $sort");
        $query->execute();
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
    
        return $mangas;
    }
    //FILTRADO
    function getByTitle($search)
    {
        $query = $this->db->prepare("SELECT * FROM manga WHERE titulo LIKE ?");
        $query->execute(["%$search%"]);
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
        return $mangas;
    }
    //PAGINADO
    function pagination($page)
    {
        $limit = 2;
        $offset = $page * $limit - $limit;
        $query = $this->db->prepare("SELECT * FROM manga LIMIT $limit OFFSET $offset");
        $query->execute();
        $mangas = $query->fetchAll(PDO::FETCH_OBJ);
        return $mangas;
    }
    // //TRAE LA CANTIDAD DE REGISTROS DE LA TABLA
    // function getRegistersQuantity()
    // {
    //     $query = $this->db->prepare("SELECT count(*) FROM manga");
    //     $query->execute();
    //     $quantity = $query->fetch(PDO::FETCH_OBJ);
    //     return $quantity;
    // }
    //TRAE TODA LA DESCRIPCION DE LA TABLA
    function getAllFieldsOfTable()
    {  
        $query = $this->db->prepare("DESCRIBE manga");
        $query->execute();
        $fields = $query->fetchAll(PDO::FETCH_OBJ);
        return $fields;
    }
}
