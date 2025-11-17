<?php
header("Content-Type: application/json");
require_once 'Modelo/conexion.php';
require_once 'Modelo/Productos.php';

// La clase DB es un Singleton, puedes obtener la instancia directamente en Producto.
// Este archivo no necesita instanciar DB.
$producto = new Producto();
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'Guardar':
        if ($producto->validate($_POST)) {
            if ($producto->guardar($_POST)) {
                $response = ['success' => true, 'message' => 'Producto guardado correctamente.', 'accion' => $accion];
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
                $response = ['success' => true, 'message' => 'Producto actualizado correctamente.', 'accion' => $accion];
            } else {
                $response['message'] = 'Error al actualizar el producto.';
            }
        } else {
            $response = ['success' => false, 'message' => 'Datos inválidos.', 'errors' => $producto->errors];
        }
        break;

    case 'Listar':
        $response = ['success' => true, 'data' => $producto->listar(), 'accion' => $accion];
        break;

    case 'CargarDatos': // Renombramos 'Buscar' para evitar confusión. Este carga datos para editar.
        $id = $_POST['id'] ?? 0;
        $data = $producto->buscarPorId($id);
        if ($data) {
            $response = ['success' => true, 'data' => $data, 'accion' => $accion];
        } else {
            $response['message'] = 'Producto no encontrado.';
        }
        break;

    // ¡NUEVO! - Case para la búsqueda por término
    case 'Buscar':
        $termino = $_POST['termino'] ?? '';
        $response = ['success' => true, 'data' => $producto->buscar($termino), 'accion' => $accion];
        break;
}

echo json_encode($response);
?>