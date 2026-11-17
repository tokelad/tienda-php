<?php

namespace services;

use models\User;


class SessionService
{
    private static ?SessionService $instance = null;
    private int $expireAfterSeconds = 3600;  

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (isset($_SESSION['lastActivity'])
            && time() - $_SESSION['lastActivity'] > $this->expireAfterSeconds) {
            $this->logout();
            session_start();
        }
        $_SESSION['lastActivity'] = time();

        $_SESSION['loggedIn'] ??= false;
        $_SESSION['isAdmin']  ??= false;
        $_SESSION['username'] ??= 'Invitado';

     
        $_SESSION['visitas'] = ($_SESSION['visitas'] ?? 0) + 1;
    }

    public static function getInstance(): SessionService
    {
        if (self::$instance === null) {
            self::$instance = new SessionService();
        }
        return self::$instance;
    }

    public function login(User $user): void
    {
        session_regenerate_id(true);           
        $_SESSION['loggedIn']  = true;
        $_SESSION['username']  = $user->username;
        $_SESSION['isAdmin']   = $user->isAdmin();
        $_SESSION['lastLogin'] = date('d/m/Y H:i:s');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return $_SESSION['loggedIn'];
    }

    public function isAdmin(): bool
    {
        return $_SESSION['isAdmin'];
    }

    public function getUsername(): string
    {
        return $_SESSION['username'];
    }

    public function getVisitas(): int
    {
        return $_SESSION['visitas'];
    }

    public function getLastLogin(): ?string
    {
        return $_SESSION['lastLogin'] ?? null;
    }
}