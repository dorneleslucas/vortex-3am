<?php
namespace source\Controller\Faqs;

use PDO;
use Source\Controller\Api;
use source\Core\Connect;
use Source\Models\Faq\FaqCategorie;

class FaqsCategories extends Api
{
    public function listAll(): void
    {
        $faq = new FaqCategorie();
        $categories = $faq->listAll();

        $this->call(
            200,
            "success",
            "Lista de categorias de FAQ",
            "success"
        )->back($categories);
    }
    public function listById(array $data): void
    {
        if (!filter_var($data["faqCategorieId"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "bad_request",
                "O id é obrigatorio e deve ser um numero inteiro",
                "error"
            )->back();
            return;
        }
        $faq = new FaqCategorie();
        $categories = $faq->listById($data["faqCategorieId"]);
        if (!$categories) {
            $this->call(
                404,
                "not_found",
                "Categoria não achada",
                "error"
            )->back();
            return;
        }
        $this->call(
            200,
            "succes",
            "Produto encontrado pelo id",
            "success"
        )->back($categories);
    }
    public function create(array $data): void
    {
        if (!isset($data["name"]) || empty($data["name"])) {
            $this->call(
                400,
                "bad_request",
                "O campo name é obrigatorio",
                "error"
            )->back();
            return;
        }
        $faq = new FaqCategorie();
        $create = $faq->create($data["name"]);

        if (!$create) {
            $this->call(
                500,
                "error",
                "Não foi possível cadastrar a categoria",
                "internal_server_error"
            )->back(null);
            return;
        }

        $this->call(
            201,
            "success",
            "Categoria de FAQ criada com sucesso",
            "created"
        )->back($create);
    }
    public function update(array $data): void
    {
        $faqId = $data["faqCategorieId"] ?? null;

        if (!filter_var($faqId, FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "error",
                "ID inválido",
                "bad_request"
            )->back();
            return;
        }

        if (empty($data["name"])) {
            $this->call(
                400,
                "error",
                "O campo name é obrigatório",
                "bad_request"
            )->back();
            return;
        }

        $faqCategory = new FaqCategorie();

        if (!$faqCategory->listById($faqId)) {
            $this->call(
                404,
                "error",
                "FAQ category não encontrado",
                "not_found"
            )->back();
            return;
        }

        $faqCategory->setId($faqId);
        $faqCategory->setName($data["name"]);

        if (!$faqCategory->update()) {
            $this->call(
                500,
                "error",
                "Erro ao atualizar o FAQ category",
                "internal_error"
            )->back();
            return;
        }

        // Busca novamente os dados atualizados
        $updatedFaq = $faqCategory->listById($faqId);

        $this->call(
            200,
            "success",
            "Categoria de FAQ atualizada com sucesso",
            "success"
        )->back($updatedFaq);
    }
    public function softDelete(array $data): void
    {
        $id = $data["faqCategorieId"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "error", "ID do FAQ category é obrigatório e deve ser um número inteiro", "bad_request")->back();
            return;
        }

        $faq = new FaqCategorie();

        if (!$faq->softDelete($id)) {
            $this->call(404, "error", "FAQ category não encontrado", "not_found")->back();
            return;
        }

        $this->call(200, "success", "FAQ category removido com sucesso", "success")->back(null);
    }
}