<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
date_default_timezone_set('America/Sao_Paulo');

ob_start();

require __DIR__ . "/vendor/autoload.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

use CoffeeCode\Router\Router;

$route = new Router(url("api"), ":");
$route->namespace("Source\Controller");

$route->get("/hello", "Api:hello");

// CRUD da entidade principal do sistema: treinos.
$route->post("/treinos", "Treinos:criar");
$route->get("/treinos", "Treinos:listar");
$route->get("/treinos/{id}", "Treinos:buscar");
$route->put("/treinos/{id}", "Treinos:atualizar");
$route->delete("/treinos/{id}", "Treinos:excluir");

$route->dispatch();

if ($route->error()) {
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "code" => 404,
        "status" => "not_found",
        "message" => "URL nao encontrada"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
