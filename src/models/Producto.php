<?php

namespace models;


class Producto
{
    public const IMAGEN_DEFAULT = 'https://placehold.co/150';

    private ?int $id;
    private ?string $uuid;
    private ?string $marca;
    private ?string $modelo;
    private ?string $descripcion;
    private ?float $precio;
    private ?int $stock;
    private ?string $imagen;
    private ?string $categoriaId;      
    private ?string $categoriaNombre;  
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?bool $isDeleted;

    public function __construct(
        ?int $id = null,
        ?string $uuid = null,
        ?string $marca = null,
        ?string $modelo = null,
        ?string $descripcion = null,
        ?float $precio = null,
        ?int $stock = null,
        ?string $imagen = null,
        ?string $categoriaId = null,
        ?string $categoriaNombre = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?bool $isDeleted = false
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->imagen = $imagen ?? self::IMAGEN_DEFAULT;   
        $this->categoriaId = $categoriaId;
        $this->categoriaNombre = $categoriaNombre;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __get(string $name): mixed
    {
        return $this->$name;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->$name = $value;
    }
}