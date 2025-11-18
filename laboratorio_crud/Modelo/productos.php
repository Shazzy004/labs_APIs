<?php
class Producto
{
    private $pdo;
    // Propiedades del producto
    public $id;
    public $codigo;
    public $producto;
    public $precio;
    public $cantidad;

    public $errors = [];

    public function __construct()
    {
        $this->pdo = DB::getInstance()->getConnection();
    }

    // Valida que los campos requeridos no estén vacíos (sin cambios)
    public function validate($data)
    {
        if (empty($data['codigo'])) {
            $this->errors['codigo'] = 'El código es obligatorio.';
        }
        if (empty($data['producto'])) {
            $this->errors['producto'] = 'El nombre del producto es obligatorio.';
        }
        if (empty($data['precio']) || !is_numeric($data['precio'])) {
            $this->errors['precio'] = 'El precio es obligatorio y debe ser un número.';
        }
        if (empty($data['cantidad']) || !is_numeric($data['cantidad'])) {
            $this->errors['cantidad'] = 'La cantidad es obligatoria y debe ser un número.';
        }
        return empty($this->errors);
    }

    // Métodos Guardar y Editar (sin cambios)
    public function guardar($data)
    {
        $sql = "INSERT INTO productos (codigo, producto, precio, cantidad) VALUES (?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([$data['codigo'], $data['producto'], $data['precio'], $data['cantidad']]);
    }

    public function editar($data)
    {
        $sql = "UPDATE productos SET codigo = ?, producto = ?, precio = ?, cantidad = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$data['codigo'], $data['producto'], $data['precio'], $data['cantidad'], $data['id']]);
    }

    // Lista todos los productos (sin cambios)
    public function listar()
    {
        $stmt = $this->pdo->query("SELECT * FROM productos ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca un producto por su ID para editarlo (sin cambios)
    public function buscarPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ¡NUEVO! - Busca productos por un término de búsqueda.
     * Busca coincidencias en los campos 'codigo' y 'producto'.
     */
    public function buscar($termino)
    {
        // Añadimos '%' para buscar coincidencias parciales
        $terminoBusqueda = '%' . $termino . '%';
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE codigo LIKE ? OR producto LIKE ? ORDER BY id DESC");
        $stmt->execute([$terminoBusqueda, $terminoBusqueda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>