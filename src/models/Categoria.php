<?php

namespace models;


class Categoria
{
    private ?string $id;         
    private ?string $nombre;
    private ?string $createdAt;   
    private ?string $updatedAt;
    private ?bool $isDeleted;

    public function __construct(
        ?string $id = null,
        ?string $nombre = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?bool $isDeleted = false
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }

    public function getId(): ?string
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