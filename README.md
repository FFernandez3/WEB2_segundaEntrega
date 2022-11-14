# API REST –RECURSO DE MANGAS
## Importar base de datos
Importar desde phpMyAdmin u otro database/db_manga.sql
## Prueba con Postman o similar
El endpoint de la API es: http://localhost/tpe_2daEntrega_version3/api/mangas 
## Obtener todos los mangas
**Method:**  GET  
**URL:**  api/mangas    
**Response:**  200  
## Obtener un manga
**Method:**  GET  
**URL:**  api/mangas/:id    
**Response:**  200  
## Insertar un nuevo manga
**Method:**  POST  
**URL:** api/mangas  
**Body:**  
`{  
        "titulo": "Spy family 4",  
        "autor": "Tatsuya Endo",  
        "sinopsis": "Los países de Westalis y Ostania libran desde hace años una guerra fría donde el espionaje y los asesinatos son moneda corriente. El espía conocido como “Twilight” es el mejor agente de Westalis que tiene por objetivo encargarse del poderoso Donovan.",  
        "editorial": "Ivrea",  
        "portada": null,  
        "id_genero_fk": 1  
    }`   
**Response:** 201
## Editar un manga
**Method:** PUT  
**URL:**  api/mangas/:id  
**Body:**    
`{  
        "titulo": "Spy family 1",  
        "autor": "Tatsuya Endo",  
        "sinopsis": "Los países de Westalis y Ostania libran desde hace años una guerra fría donde el espionaje y los asesinatos son moneda corriente. El espía conocido como “Twilight” es el mejor agente de Westalis que tiene por objetivo encargarse del poderoso Donovan.",  
        "editorial": "Panini",  
        "portada": null,  
        "id_genero_fk": 1  
    }`    
**Response:** 201
## Eliminar un manga
**Method:** DELETE  
**Url:** api/mangas/:id  
**Response:** 200  
## Filtrar por titulo
Esta funcionalidad permite buscar un recurso por una parte de su titulo.  
**Method:** GET  
**Url:** api/mangas?search=alic  
**Response:** 200  
## Ordenar por cualquier campo
Esta funcionalidad permite obtener todos los recursos ordenados ascendentemente por cualquiera de los campos de la tabla del recurso.  
**Method:** GET  
**Url:**  api/mangas?sort=autor  
**Response:** 200  
## Paginar todos los mangas
Esta funcionalidad permite  mostrar dos  mangas por página.   
**Method:** GET  
**Url:** api/mangas?page=1  
**Response:** 200  
## Ordenar y paginar
Esta funcionalidad permite ordenar los recursos según cualquier campo de la tabla y mostrarlos paginados.   
**Method:** GET  
**Url:** api/mangas?sort=titulo&page=1  
**Response:** 200  
## Ordenar y filtrar 
Esta funcionalidad permite ordenar los recursos según cualquier campo de la tabla y filtrarlos por titulo.  
**Method:** GET  
**Url:** api/mangas?sort=titulo&search=bea  
**Response:** 200  
## Paginar y filtrar 
Esta funcionalidad permite paginar los recursos filtrados por título.   
**Method:** GET  
**Url:** api/mangas?page=1&search=spy  
**Response:** 200  
## Filtrar, ordenar y paginar 
Esta funcionalidad permite ordenar los recursos según cualquier campo de la tabla, filtrarlos por título y paginarlos.   
**Method:** GET  
**Url:** api/mangas?search=ali&sort=autor&page=1  
**Response:**  200  
## ERRORES
- Key errónea  
**Method:** GET  
**Url:** api/mangas?sor=titulo  
**Response:** 404  
**Mensaje de error:** Se ingresaron parametros incorrectos, ingrese Sort, Page, Search o cualquier combinacion posible.  
- Value vacio  
**Method:** GET  
**Url:** api/mangas?sort=&page=1  
**Response:** 400  
**Mensaje de error:** Alguno de los parametros es erroneo o esta vacio.
## CODIGOS DE RESPUESTA HTTP
### 200 OK
Se da cuando una solicitud realizada por el usuario tuvo éxito.   
### 201 CREATED
Es la respuesta cuando se ha modificado o creado un recurso exitosamente.   
### 400 BAD REQUEST
Indica que el servidor no puede o no procesara la petición debido a que algo es percibido como un error del cliente.   
### 404 NOT FOUND 
Indica que una página buscada no puede ser encontrada aunque la petición este correctamente hecha.  
### 500 INTERNAL SERVER ERROR
 Indica que el servidor encontró una condición inesperada que le impide completar la petición.  








