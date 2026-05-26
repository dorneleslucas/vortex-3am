<?php

namespace source\Controller\Faqs;

use Source\Controller\Api;
use Source\Models\Faq\Faq;

class Faqs extends Api
{
    public function listAll(): void
    {
        $faq = new Faq();
        $questions = $faq->listAll();
        $this->call(
            200,
            "success",
            "Lista de FAQs",
            "success"
        )->back($questions);
    }
    public function listById(array $data): void
    {
        if (!filter_var($data["faq_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "bad_request",
                "Id invalido",
                "error"
            )->back();
            return;
        }
        $faq = new Faq();
        $question = $faq->listById($data["faq_id"]);

        if (!$question) {
            $this->call(
                404,
                "not_found",
                "Produto nao encontrado",
                "error"
            )->back();
            return;
        }
        $this->call(
            200,
            "success",
            "Produto encontrado pelo id",
            "success"
        )->back($question);
    }
public function create(array $data): void
{
    if (
        !isset($data["question"]) || empty($data["question"]) ||
        !isset($data["answer"]) || empty($data["answer"]) ||
        !isset($data["faqs_category_id"]) || empty($data["faqs_category_id"])
    ) {
        $this->call(
            400,
            "bad_request",
            "Todos os campos são obrigatórios",
            "error"
        )->back();
        return;
    }

    $faq = new Faq();
    $create = $faq->create(
        $data["question"],
        $data["answer"],
        (int)$data["faqs_category_id"]
    );

    if (!$create) {
        $this->call(
            500,
            "error",
            "Não foi possível cadastrar o FAQ",
            "internal_server_error"
        )->back(null);
        return;
    }

    $this->call(
        201,
        "success",
        "FAQ criada com sucesso",
        "created"
    )->back($create);
}
    public function update(array $data): void
    {
        $faqId = $data["faq_id"] ?? null;

        if (!filter_var($faqId, FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "error",
                "ID inválido",
                "bad_request"
            )->back();
            return;
        }

        $body = json_decode(file_get_contents("php://input"), true);

        if (
            !$body ||
            empty($body["question"]) ||
            empty($body["answer"]) ||
            empty($body["faqs_category_id"])
        ) {
            $this->call(
                400,
                "error",
                "ID inválido ou campos obrigatórios ausentes",
                "bad_request"
            )->back();
            return;
        }

        $faq = new Faq();

        $exists = $faq->listById($faqId);
        if (!$exists) {
            $this->call(
                404,
                "error",
                "FAQ não encontrado",
                "not_found"
            )->back();
            return;
        }

        $faq->setId($faqId);
        $faq->setQuestion($body["question"]);
        $faq->setAnswer($body["answer"]);
        $faq->setFaqs_category_id($body["faqs_category_id"]);

        $success = $faq->update();

        if (!$success) {
            $this->call(
                500,
                "error",
                "Erro ao atualizar o FAQ",
                "internal_error"
            )->back();
            return;
        }

        $updatedFaq = $faq->listById($faqId);

        $this->call(
            200,
            "success",
            "FAQ atualizado com sucesso",
            "success"
        )->back($updatedFaq);
    }
    public function softDelete(array $data): void
    {
        $id = $data["faq_id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "error", "ID do FAQ é obrigatório e deve ser um número inteiro", "bad_request")->back();
            return;
        }

        $faq = new Faq();

        if (!$faq->softDelete($id)) {
            $this->call(404, "error", "FAQ não encontrado", "not_found")->back();
            return;
        }

        $this->call(200, "success", "FAQ removido com sucesso", "success")->back(null);
    }
}