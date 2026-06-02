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
$route->get("/users/list", "Users:usersList");

// Produtos
$route->get("/products/list", "Products\\Products:productsList");
$route->get("/products/list/{productId}", "Products\\Products:productById");
$route->post("/products", "Products\\Products:create");
$route->put("/products/{product_id}", "Products\\Products:update");
$route->delete("/products/{product_id}", "Products\\Products:softDelete");

// Categorias de produtos
$route->get("/products-categories/list", "Products\\ProductsCategories:productsCategoryList");
$route->get("/products-categories/list/{categoryId}", "Products\\ProductsCategories:categoryFindById");
$route->post("/products-categories", "Products\\ProductsCategories:create");
$route->get("/products-categories/list", "Products\\ProductsCategories:productsCategoryList");
$route->get("/products-categories/list/{categoryId}", "Products\\ProductsCategories:categoryFindById");
$route->post("/products-categories", "Products\\ProductsCategories:create");

// Categorias de FAQ
$route->get("/faqs-categories/list", "Faqs\\FaqsCategories:listAll");
$route->get("/faqs-categories/list/{faq_categorieId}", "Faqs\\FaqsCategories:listById");
$route->post("/faqs-categories", "Faqs\\FaqsCategories:create");
$route->put("/faqs-categories/{faq_category_id}", "Faqs\\FaqsCategories:update");
$route->delete("/faqs-categories/{faq_category_id}", "Faqs\\FaqsCategories:softDelete");
$route->get("/faqs-categories/list", "Faqs\\FaqsCategories:listAll");
$route->get("/faqs-categories/list/{faq_categorieId}", "Faqs\\FaqsCategories:listById");
$route->post("/faqs-categories", "Faqs\\FaqsCategories:create");
$route->put("/faqs-categories/{faq_category_id}", "Faqs\\FaqsCategories:update");
$route->delete("/faqs-categories/{faq_category_id}", "Faqs\\FaqsCategories:softDelete");

// FAQs
$route->get("/faqs/list", "Faqs\\Faqs:listAll");
$route->get("/faqs/list/{faq_id}", "Faqs\\Faqs:listById");
$route->post("/faqs", "Faqs\\Faqs:create");
$route->put("/faqs/{faq_id}", "Faqs\\Faqs:update");
$route->delete("/faqs/{faq_id}", "Faqs\\Faqs:softDelete");

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
