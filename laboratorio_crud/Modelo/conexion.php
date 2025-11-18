<?php
// Clase para la conexión a la base de datos (Singleton)
class DB
{
    private static $instance = NULL;
    private $pdo;

    private function __construct()
    {
        $host = 'localhost';
        $dbname = 'productosdb';
        $user = 'root'; // Usuario por defecto en XAMPP
        $pass = '';     // Contraseña por defecto en XAMPP

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    // Método para ejecutar consultas preparadas (INSERT, UPDATE, DELETE)
    public function execute($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Método para obtener resultados (SELECT)
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>