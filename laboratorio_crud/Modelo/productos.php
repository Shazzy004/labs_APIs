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
        // Obtenemos la instancia de la conexión a la BD
        $this->pdo = DB::getInstance()->getConnection();
    }

    // Valida que los campos requeridos no estén vacíos
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

    // Guarda un nuevo producto
    public function guardar($data)
    {
        $sql = "INSERT INTO productos (codigo, producto, precio, cantidad) VALUES (?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $data['codigo'],
            $data['producto'],
            $data['precio'],
            $data['cantidad']
        ]);
    }

    // Edita un producto existente
    public function editar($data)
    {
        $sql = "UPDATE productos SET codigo = ?, producto = ?, precio = ?, cantidad = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([
            $data['codigo'],
            $data['producto'],
            $data['precio'],
            $data['cantidad'],
            $data['id']
        ]);
    }

    // Lista todos los productos
    public function listar()
    {
        $stmt = $this->pdo->query("SELECT * FROM productos ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca un producto por su ID
    public function buscarPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>