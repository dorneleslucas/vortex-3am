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
$route->group("/users");
$route->post("/register","Users:register"); // Registrar usuário comum
$route->post("/login","Users:auth"); // login de usuário comum
$route->put("/update","Users:update"); // update de usuário comum
$route->post("/register-admin","Users:registerAdmin"); // Registrar usuário admin NÃO IMPLEMENTADO
$route->post("/login-admin","Users:authAdmin"); // login de usuário admin
$route->put("/update-admin","Users:updateAdmin"); // update de usuário admin
$route->group(null);

$route->group("/products");
$route->get("/list", "Products\\Products:productsList");
$route->get("/list/{productId}", "Products\\Products:productById");
$route->post("/", "Products\\Products:create");
$route->put("/{product_id}", "Products\\Products:update");
$route->delete("/{product_id}", "Products\\Products:softDelete");
$route->group(null);

$route->group("/products_categories");
$route->get("/list/{categoryId}", "Products\\ProductsCategories:categoryFindById");
$route->get("/list", "Products\\ProductsCategories:productsCategoryList");
$route->post("/", "Products\\ProductsCategories:create");
$route->group(null);

$route->group("/faqs_categories");
$route->get("/list", "Faqs\\FaqsCategories:listAll");
$route->get("/list/{faqCategorieId}", "Faqs\\FaqsCategories:listById");
$route->post("/", "Faqs\\FaqsCategories:create");
$route->put("/{faqCategorieId}", "Faqs\\FaqsCategories:update");
$route->delete("/{faqCategorieId}", "Faqs\\FaqsCategories:softDelete");
$route->group(null);

$route->group("/faqs");
$route->get("/list", "Faqs\\Faqs:listAll");
$route->get("/listFaq", "Faqs\\Faqs:selectFaq");
$route->get("/list/{faq_id}", "Faqs\\Faqs:listById");
$route->post("/", "Faqs\\Faqs:create");
$route->put("/{faq_id}", "Faqs\\Faqs:update");
$route->delete("/{faq_id}", "Faqs\\Faqs:softDelete");
$route->group(null);


// CRUD da entidade principal do sistema: treinos.
$route->post("/treinos", "Treinos:criar");
$route->get("/treinos", "Treinos:listar");
$route->get("/treinos/{id}", "Treinos:buscar");
$route->put("/treinos/{id}", "Treinos:atualizar");
$route->delete("/treinos/{id}", "Treinos:excluir");
$route->group(null);


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
