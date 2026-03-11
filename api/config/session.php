<?php
// Compatibilidad API -> reusar Auth del panel.
require_once __DIR__ . '/../../app/Core/Auth.php';

// Alias de funciones para mantener API estable.
function loginUser(array $user): void
{
    Auth::login($user);
}

function logoutUser(): void
{
    Auth::logout();
}

function isLogged(): bool
{
    return Auth::isLogged();
}

function getUserRole(): ?string
{
    return Auth::role();
}

function getUserId(): ?int
{
    return Auth::userId();
}

function getUsername(): ?string
{
    Auth::startSession();
    return isset($_SESSION['user_name']) ? (string) $_SESSION['user_name'] : (isset($_SESSION['usuario']) ? (string) $_SESSION['usuario'] : null);
}


