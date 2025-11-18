<?php
// models/Producto.php

class Producto
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Actividad 1: Implementar GET (para listar todos)
    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM productos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para crear un nuevo producto (POST)
    public function create($data)
    {
        $sql = "INSERT INTO productos (codigo, producto, precio, cantidad) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data->codigo,
            $data->producto,
            $data->precio,
            $data->cantidad
        ]);
    }

    // Actividad 2: Implementar PUT (para actualizar)
    public function update($id, $data)
    {
        $sql = "UPDATE productos SET codigo = ?, producto = ?, precio = ?, cantidad = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data->codigo,
            $data->producto,
            $data->precio,
            $data->cantidad,
            $id
        ]);
    }
}
?>