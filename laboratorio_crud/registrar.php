<?php
// Encabezado para indicar que la respuesta será en formato JSON
header("Content-Type: application/json");

// Incluimos las clases
require_once 'Modelo/conexion.php';
require_once 'Modelo/Productos.php';

$producto = new Producto();
$response = ['success' => false, 'message' => 'Acción no reconocida.'];

// Usamos el operador de fusión de null para manejar $_POST['accion'] de forma segura
$accion = $_POST['accion'] ?? '';

// Estructura switch para manejar las diferentes acciones
switch ($accion) {
    case 'Guardar':
        if ($producto->validate($_POST)) {
            if ($producto->guardar($_POST)) {
                $response = ['success' => true, 'message' => 'Producto guardado correctamente.'];
            } else {
                $response['message'] = 'Error al guardar el producto.';
            }
        } else {
            $response = ['success' => false, 'message' => 'Datos inválidos.', 'errors' => $producto->errors];
        }
        break;

    case 'Editar':
        if ($producto->validate($_POST)) {
            if ($producto->editar($_POST)) {
                $response = ['success' => true, 'message' => 'Producto actualizado correctamente.'];
            } else {
                $response['message'] = 'Error al actualizar el producto.';
            }
        } else {
            $response = ['success' => false, 'message' => 'Datos inválidos.', 'errors' => $producto->errors];
        }
        break;

    case 'Listar':
        $response = ['success' => true, 'data' => $producto->listar()];
        break;

    case 'Buscar':
        $id = $_POST['id'] ?? 0;
        $data = $producto->buscarPorId($id);
        if ($data) {
            $response = ['success' => true, 'data' => $data];
        } else {
            $response['message'] = 'Producto no encontrado.';
        }
        break;

}

// Enviamos la respuesta JSON al cliente
echo json_encode($response);
?>