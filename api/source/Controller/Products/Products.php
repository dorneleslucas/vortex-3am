<?php

namespace source\Controller\Products;

use Source\Controller\Api;
use Source\Models\Product\Product;

class Products extends Api
{
    public function productsList(): void
    {
        $products = new Product();
        $this->call(200, "success", "Lista de Produtos", "success")->back($products->listAll());
    }

    public function productById(array $data): void
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
        $product = $product->productById($data["productId"]);

        if (!$product) {
            $this->call(
                404,
                "not_found",
                "Produto não encontrado",
                "error"
            )->back();
            return;
        }

        $this->call(200, "success", "Produto encontrado", "success")->back($product);


    }

    public function create(array $data): void
    {
        if (
            empty($data["name"]) ||
            empty($data["price"]) ||
            empty($data["categoryId"])
        ) {
            $this->call(400, "bad_request", "name, price e categoryId são obrigatórios", "error")->back();
            return;
        }

        $product = new Product(
            null,
            $data["categoryId"],
            $data["name"],
            $data["price"]
        );

        if (!$product->create()) {
            $this->call(500, "internal_error", "Erro ao criar produto", "error")->back();
            return;
        }

        $this->call(201, "created", "Produto criado", "success")->back([
            "id" => $product->getId(),
            "categoryId" => $product->getCategoryId(),
            "name" => $product->getName(),
            "price" => $product->getPrice(),
        ]);
    }

    public function update(array $data): void
    {
        $productId = $data["product_id"] ?? null;

        if (!filter_var($productId, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);

        if (
            empty($body["name"]) ||
            empty($body["price"]) ||
            empty($body["categoryId"])
        ) {
            $this->call(400, "bad_request", "Campos name, price e categoryId são obrigatórios", "error")->back();
            return;
        }

        $product = new Product();

        if (!$product->productById($productId)) {
            $this->call(404, "not_found", "Produto não encontrado", "error")->back();
            return;
        }

        $product->setId($productId);
        $product->setName($body["name"]);
        $product->setPrice($body["price"]);
        $product->setCategoryId($body["categoryId"]);

        if (!$product->update()) {
            $this->call(500, "internal_error", "Erro ao atualizar produto", "error")->back();
            return;
        }

        $this->call(200, "success", "Produto atualizado com sucesso", "success")
            ->back($product->productById($productId));
    }
    public function softDelete(array $data): void
    {
        $id = $data["product_id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "error", "ID do produto é obrigatório e deve ser um número inteiro", "bad_request")->back();
            return;
        }

        $product = new Product();

        if (!$product->softDelete($id)) {
            $this->call(404, "error", "Produto não encontrado", "not_found")->back();
            return;
        }

        $this->call(200, "success", "Produto removido com sucesso", "success")->back(null);
    }
}