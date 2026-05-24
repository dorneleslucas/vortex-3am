<?php

namespace source\Controller;

use Source\Controller\Api;
use Source\Models\Product;

class Products extends Api
{
    public function productsList(): void
    {
        $products = new Product();
        $this->call(200, "success", "Lista de Produtos", "success")->back($products->listAll());
    }

    public function productsListById(array $data): void
    {
        if (!filter_var($data["productId"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "bad_request",
                "ID do produto é obrigatório e deve ser um número inteiro",
                "error"
            )->back();
            return;
        }

        $product = new Product();
        $product = $product->listById($data["productId"]);

        if (!$product) {
            $this->call(
                404,
                "not_found",
                "Produto não encontrado",
                "error"
            )->back();
            return;
        }

        $this->call(200, "success", "Produto encontrado", "success")
            ->back($product);
    }

    public function create(array $data): void
    {
        // Verifica se os campos obrigatórios existem 
        if (!isset($data["name"]) || empty($data["name"])) {
            $this->call(
                400,
                "bad_request",
                "O campo name é obrigatório",
                "error"
            )->back();
            return;
        }

        if (!isset($data["price"]) || empty($data["price"])) {
            $this->call(
                400,
                "bad_request",
                "O campo price é obrigatório",
                "error"
            )->back();
            return;
        }

        // Verifica a categoria (aceita category_id OU categoryId)
        $categoryId = null;
        if (isset($data["category_id"])) {
            $categoryId = $data["category_id"];
        } elseif (isset($data["categoryId"])) {
            $categoryId = $data["categoryId"];
        }

        if (!$categoryId) {
            $this->call(
                400,
                "bad_request",
                "O campo category_id ou categoryId é obrigatório",
                "error"
            )->back();
            return;
        }

        // Cria o produto 
        $produto = new Product(
            null,
            $categoryId,
            $data["name"],
            $data["price"]
        );

        if (!$produto->insert()) {
            $this->call(
                500,
                "internal_error",
                "Erro ao inserir produto no banco de dados",
                "error"
            )->back();
            return;
        }

        $response = [
            "id" => $produto->getId(),
            "categoryId" => $produto->getCategoryId(),
            "name" => $produto->getName(),
            "price" => $produto->getPrice()
        ];

        $this->call(
            201,
            "created",
            "Produto cadastrado com sucesso",
            "success"
        )->back($response);
    }
}