<?php

namespace services;

use PDO;
use models\Categoria;

class CategoriasService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("select * from categorias where is_deleted = false order by nombre;");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $modelArr = [];
        foreach ($categorias as $el) {
            $categoria = new Categoria($el["id"], $el["nombre"], $el["created_at"], $el["updated_at"], $el["is_deleted"]);
            $modelArr[] = $categoria;
        }

        return $modelArr;
    }

    public function findByName(string $nombre): ?Categoria
    {

        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
        $stmt->execute(["nombre" => $nombre]);
        $el = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($el === false) {
            return null;
        }
        return new Categoria($el["id"], $el["nombre"], $el["created_at"], $el["updated_at"], $el["is_deleted"]);
    }
}
