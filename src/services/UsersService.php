<?php

namespace services;

use Exception;
use PDO;
use models\User;


class UsersService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

  
    public function authenticate(string $username, string $password): User
    {
        $user = $this->findUserByUsername($username);

        if ($user === null || !password_verify($password, $user->password)) {
            throw new Exception("Usuario o contraseña incorrectos");
        }
        return $user;
    }

    public function findUserByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE username = :username AND is_deleted = false"
        );
        $stmt->execute(["username" => $username]);
        $fila = $stmt->fetch();
        if ($fila === false) {
            return null;
        }

        $stmtRoles = $this->db->prepare("SELECT roles FROM user_roles WHERE user_id = :id");
        $stmtRoles->execute(["id" => $fila["id"]]);
        $roles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

        return new User(
            id: (int) $fila["id"],
            username: $fila["username"],
            password: $fila["password"],
            nombre: $fila["nombre"],
            apellidos: $fila["apellidos"],
            email: $fila["email"],
            createdAt: $fila["created_at"],
            updatedAt: $fila["updated_at"],
            isDeleted: (bool) $fila["is_deleted"],
            roles: $roles
        );
    }
}