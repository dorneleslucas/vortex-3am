<?php

namespace Source\Controller;

use Source\Models\Aluno;


class Alunos extends Api
{
    public function criar(array $data): void
    {
        if (!$this->dadosObrigatoriosValidos($data)) {
            $this->call(400, "bad_request", "personalId e nome são obrigatórios", "error")->back();
            return;
        }

        $aluno = new Aluno(
            null,
            (int) $data["personal_id"],
            trim($data["nome"]),
            $data["email"] ?? null,
            $data["telefone"] ?? null,
            $data["objetivo"] ?? null,
            $data["ativo"] ?? 1
        );

        if (!$aluno->criar()) {
            $this->call(500, "internal_error", "Erro ao cadastrar aluno", "error")->back();
            return;
        }

        $this->call(201, "created", "Aluno cadastrado com sucesso", "success")
            ->back($aluno->buscarPorId((int) $aluno->getId()));
    }

    public function listar(): void
    {
        $aluno = new Aluno();

        $this->call(200, "success", "Lista de alunos", "success")
            ->back($aluno->listar());
    }

    public function buscar(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do aluno inválido", "error")->back();
            return;
        }

        $aluno = new Aluno();
        $registro = $aluno->buscarPorId((int) $id);

        if (!$registro) {
            $this->call(404, "not_found", "Aluno não encontrado", "error")->back();
            return;
        }

        $this->call(200, "success", "Aluno encontrado", "success")->back($registro);
    }

    public function buscarPorPersonal(array $data): void
    {
        $personalId = $data["personal_id"] ?? null;

        if (!filter_var($personalId, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do personal inválido", "error")->back();
            return;
        }

        $aluno = new Aluno();
        $registros = $aluno->buscarPorPersonal((int) $personalId);

        $this->call(200, "success", "Alunos do personal", "success")->back($registros);
    }

    public function atualizar(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do aluno inválido", "error")->back();
            return;
        }

        if (!$this->dadosObrigatoriosValidos($data)) {
            $this->call(400, "bad_request", "personal_id e nome são obrigatórios", "error")->back();
            return;
        }

        $aluno = new Aluno();

        if (!$aluno->buscarPorId((int) $id)) {
            $this->call(404, "not_found", "Aluno não encontrado", "error")->back();
            return;
        }

        $aluno = new Aluno(
            (int) $id,
            (int) $data["personal_id"],
            trim($data["nome"]),
            $data["email"] ?? null,
            $data["telefone"] ?? null,
            $data["objetivo"] ?? null,
            $data["ativo"] ?? 1
        );

        if (!$aluno->atualizar()) {
            $this->call(500, "internal_error", "Erro ao atualizar aluno", "error")->back();
            return;
        }

        $this->call(200, "success", "Aluno atualizado com sucesso", "success")
            ->back($aluno->buscarPorId((int) $id));
    }

    public function excluir(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do aluno inválido", "error")->back();
            return;
        }

        $aluno = new Aluno();

        if (!$aluno->excluir((int) $id)) {
            $this->call(404, "not_found", "Aluno não encontrado", "error")->back();
            return;
        }

        $this->call(200, "success", "Aluno excluído com sucesso", "success")->back();
    }

    private function dadosObrigatoriosValidos(array $data): bool
    {
        return !empty($data["personal_id"])
            && filter_var($data["personal_id"], FILTER_VALIDATE_INT)
            && !empty($data["nome"]);
    }
}
