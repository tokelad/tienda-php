<?php

namespace models;


class User
{
    private ?int $id;
    private ?string $username;
    private ?string $password;
    private ?string $nombre;
    private ?string $apellidos;
    private ?string $email;
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?bool $isDeleted;
    private array $roles;

    public function __construct(
        ?int $id = null,
        ?string $username = null,
        ?string $password = null,
        ?string $nombre = null,
        ?string $apellidos = null,
        ?string $email = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?bool $isDeleted = false,
        array $roles = []
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
        $this->roles = $roles;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAdmin(): bool
    {
        foreach ($this->roles as $rol) {
            if ($rol === 'ADMIN') {
                return true;
            }
        }
        return false;
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
