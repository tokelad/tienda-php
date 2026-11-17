<?php

namespace services;

use PDO;
use models\Producto;
use Ramsey\Uuid\Uuid;


class ProductosService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAllWithCategoryName(string $searchTerm = ""): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
         FROM productos p
         JOIN categorias c ON p.categoria_id = c.id
         WHERE p.is_deleted = false
           AND (p.marca ILIKE :busqueda1 OR p.modelo ILIKE :busqueda2)
         ORDER BY p.id"
        );
        $stmt->execute([
            "busqueda1" => "%$searchTerm%",
            "busqueda2" => "%$searchTerm%",
        ]);

        $productos = [];
        foreach ($stmt->fetchAll() as $fila) {
            $productos[] = $this->filaAProducto($fila);
        }
        return $productos;
    }

    public function findById(int $id): ?Producto
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             JOIN categorias c ON p.categoria_id = c.id
             WHERE p.id = :id AND p.is_deleted = false"
        );
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fila === false) {
            return null;
        }
        return $this->filaAProducto($fila);
    }

    public function save(Producto $producto): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO productos (uuid, marca, modelo, descripcion, precio, stock, imagen, categoria_id)
             VALUES (:uuid, :marca, :modelo, :descripcion, :precio, :stock, :imagen, :categoria_id)"
        );
        $stmt->execute([
            "uuid"         => Uuid::uuid4()->toString(),
            "marca"        => $producto->marca,
            "modelo"       => $producto->modelo,
            "descripcion"  => $producto->descripcion,
            "precio"       => $producto->precio,
            "stock"        => $producto->stock,
            "imagen"       => Producto::IMAGEN_DEFAULT,
            "categoria_id" => $producto->categoriaId,
        ]);
    }

    public function update(Producto $producto): void
    {
        $stmt = $this->db->prepare(
            "UPDATE productos
             SET marca = :marca, modelo = :modelo, descripcion = :descripcion,
                 precio = :precio, stock = :stock, imagen = :imagen,
                 categoria_id = :categoria_id, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id"
        );
        $stmt->execute([
            "marca"        => $producto->marca,
            "modelo"       => $producto->modelo,
            "descripcion"  => $producto->descripcion,
            "precio"       => $producto->precio,
            "stock"        => $producto->stock,
            "imagen"       => $producto->imagen,
            "categoria_id" => $producto->categoriaId,
            "id"           => $producto->id,
        ]);
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function filaAProducto(array $fila): Producto
    {
        return new Producto(
            id: (int) $fila["id"],
            uuid: $fila["uuid"],
            marca: $fila["marca"],
            modelo: $fila["modelo"],
            descripcion: $fila["descripcion"],
            precio: (float) $fila["precio"],
            stock: (int) $fila["stock"],
            imagen: $fila["imagen"],
            categoriaId: $fila["categoria_id"],
            categoriaNombre: $fila["categoria_nombre"],
            createdAt: $fila["created_at"],
            updatedAt: $fila["updated_at"],
            isDeleted: (bool) $fila["is_deleted"]
        );
    }
}
