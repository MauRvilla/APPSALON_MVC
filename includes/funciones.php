<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo){
    if ($actual !== $proximo) {
        return true;
    }
    return false;
}

// Funcion para revisar que los usuarios esten autenticados
function isAuth():void{
    if (!isset($_SESSION['login'])) {
        header('Location: /');
    }
}

function isAdmin():void{
    if (!isset($_SESSION['admin'])) {
        header('Location: /');
    }
}