<?php

namespace Source\Controller;

use Source\Models\Exercicio;

class Exercicios extends Api
{
    public function listar(): void
    {
        $exercicio = new Exercicio();
        $this->call(200, "success", "Lista de exercícios", "success")
            ->back($exercicio->listar());
    }

    public function buscar(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do exercício inválido", "error")->back();
            return;
        }

        $exercicio = new Exercicio();
        $registro = $exercicio->buscarPorId((int) $id);

        if (!$registro) {
            $this->call(404, "not_found", "Exercício não encontrado", "error")->back();
            return;
        }

        $this->call(200, "success", "Exercício encontrado", "success")->back($registro);
    }

    public function criar(array $data): void
    {
        if (empty($data["nome"])) {
            $this->call(400, "bad_request", "O campo 'nome' é obrigatório", "error")->back();
            return;
        }

        $exercicio = new Exercicio(
            null,
            trim($data["nome"]),
            $data["categoria"] ?? null,
            $data["descricao"] ?? null,
            $data["ativo"] ?? 1
        );

        if (!$exercicio->criar()) {
            $this->call(500, "internal_error", "Erro ao cadastrar exercício", "error")->back();
            return;
        }

        $this->call(201, "created", "Exercício cadastrado com sucesso", "success")
            ->back($exercicio->buscarPorId((int) $exercicio->getId()));
    }

    public function atualizar(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do exercício inválido", "error")->back();
            return;
        }

        if (empty($data["nome"])) {
            $this->call(400, "bad_request", "O campo 'nome' é obrigatório", "error")->back();
            return;
        }

        $exercicio = new Exercicio();
        if (!$exercicio->buscarPorId((int) $id)) {
            $this->call(404, "not_found", "Exercício não encontrado", "error")->back();
            return;
        }

        $exercicio->setId((int) $id);
        $exercicio->setNome(trim($data["nome"]));
        $exercicio->setCategoria($data["categoria"] ?? null);
        $exercicio->setDescricao($data["descricao"] ?? null);
        $exercicio->setAtivo($data["ativo"] ?? 1);

        if (!$exercicio->atualizar()) {
            $this->call(500, "internal_error", "Erro ao atualizar exercício", "error")->back();
            return;
        }

        $this->call(200, "success", "Exercício atualizado com sucesso", "success")
            ->back($exercicio->buscarPorId((int) $id));
    }

    public function excluir(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do exercício inválido", "error")->back();
            return;
        }

        $exercicio = new Exercicio();
        if (!$exercicio->excluir((int) $id)) {
            $this->call(404, "not_found", "Exercício não encontrado ou já inativo", "error")->back();
            return;
        }

        $this->call(200, "success", "Exercício removido com sucesso", "success")->back();
    }
}