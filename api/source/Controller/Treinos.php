<?php

namespace Source\Controller;

use Source\Models\Treino;

class Treinos extends Api
{


    public function criar(array $data): void
    {
        

        if (!$this->dadosObrigatoriosValidos($data)) {
            $this->call(400, "bad_request", "alunoId e titulo sao obrigatorios", "error")->back();
            return;
        }

        $treino = new Treino(
            null,
            (int) $data["aluno_id"],
            trim($data["titulo"]),
            $data["objetivo"] ?? null,
            $data["observacoes"] ?? null,
            $data["dataInicio"] ?? null,
            $data["dataFim"] ?? null,
            $data["status"] ?? "ativo"
        );

        if (!$treino->criar()) {
            $this->call(500, "internal_error", "Erro ao cadastrar treino", "error")->back();
            return;
        }

        $this->call(201, "created", "Treino cadastrado com sucesso", "success")
            ->back($treino->buscarPorId((int) $treino->getId()));
    }



    private function dadosObrigatoriosValidos(array $data): bool
    {
        return !empty($data["aluno_id"])
            && filter_var($data["aluno_id"], FILTER_VALIDATE_INT)
            && !empty($data["titulo"]);
    }



    public function listar(): void
    {
        $treino = new Treino();

        $this->call(200, "success", "Lista de treinos", "success")
            ->back($treino->listar());
    }

    public function buscar(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do treino invalido", "error")->back();
            return;
        }

        $treino = new Treino();
        $registro = $treino->buscarPorId((int) $id);

        if (!$registro) {
            $this->call(404, "not_found", "Treino nao encontrado", "error")->back();
            return;
        }

        $this->call(200, "success", "Treino encontrado", "success")->back($registro);
    }

    
    public function atualizar(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do treino invalido", "error")->back();
            return;
        }
        if (!$this->dadosObrigatoriosValidos($data)) {
            $this->call(400, "bad_request", "aluno_id e titulo sao obrigatorios", "error")->back();
            return; 
        }

        $treino = new Treino();

        if (!$treino->buscarPorId((int) $id)) {
            $this->call(404, "not_found", "Treino nao encontrado", "error")->back();
            return;
        }

        $treino = new Treino(
            (int) $id,
            (int) $data["aluno_id"],
            trim($data["titulo"]),
            $data["objetivo"] ?? null,
            $data["observacoes"] ?? null,
            $data["dataInicio"] ?? null,
            $data["dataFim"] ?? null,
            $data["status"] ?? "ativo"
        );

        if (!$treino->atualizar()) {
            $this->call(500, "internal_error", "Erro ao atualizar treino", "error")->back();
            return;
        }

        $this->call(200, "success", "Treino atualizado com sucesso", "success")
            ->back($treino->buscarPorId((int) $id));
    }

    public function excluir(array $data): void
    {
        $id = $data["id"] ?? null;

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do treino invalido", "error")->back();
            return;
        }

        $treino = new Treino();

        if (!$treino->excluir((int) $id)) {
            $this->call(404, "not_found", "Treino nao encontrado", "error")->back();
            return;
        }

        $this->call(200, "success", "Treino excluido com sucesso", "success")->back();
    }

    private function requestData(array $routeData = []): array
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (is_array($json) && !empty($json)) {
            return $json;
        }

        return $routeData;
    }
    
            
}
