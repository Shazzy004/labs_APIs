<?php
// ProductosController.php

// Encabezados para permitir el acceso a la API (CORS) y definir el tipo de contenido
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluimos los modelos
require_once 'models/Database.php';
require_once 'models/Producto.php';

$productoModel = new Producto();

// Obtenemos el método de la solicitud (GET, POST, PUT, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// Estructura de control switch para manejar los diferentes métodos
switch ($method) {
    case 'GET':
        // Actividad 1: Implementar el método GET
        $resultado = $productoModel->getAll();
        http_response_code(200); // OK
        echo json_encode($resultado);
        break;

    case 'POST':
        // Implementación guiada del método POST
        // Obtenemos los datos del cuerpo de la solicitud (en formato JSON)
        $data = json_decode(file_get_contents("php://input"));

        // Verificación básica de datos
        if (!empty($data->codigo) && !empty($data->producto) && !empty($data->precio) && !empty($data->cantidad)) {
            if ($productoModel->create($data)) {
                http_response_code(201); // Creado
                echo json_encode(["message" => "Producto creado exitosamente."]);
            } else {
                http_response_code(503); // Servicio no disponible
                echo json_encode(["message" => "No se pudo crear el producto."]);
            }
        } else {
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(["message" => "Datos incompletos. No se pudo crear el producto."]);
        }
        break;

    case 'PUT':
        // Actividad 2: Implementar el método PUT
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $data = json_decode(file_get_contents("php://input"));

        if ($id === null) {
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(["message" => "Se requiere un ID para actualizar."]);
            exit();
        }

        if ($productoModel->update($id, $data)) {
            http_response_code(200); // OK
            echo json_encode(["message" => "Producto actualizado exitosamente."]);
        } else {
            http_response_code(503); // Servicio no disponible
            echo json_encode(["message" => "No se pudo actualizar el producto."]);
        }
        break;

    default:
        // Si se usa un método no permitido
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."]);
        break;
}
?>