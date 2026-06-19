<?php

namespace Source\Models;

use Source\Core\Connect;

class Treino
{
    private ?int $id;
    private ?int $alunoId;
    private ?string $titulo;
    private ?string $objetivo;
    private ?string $observacoes;
    private ?string $dataInicio;
    private ?string $dataFim;
    private ?string $status;

    public function __construct(
        ?int $id = null,
        ?int $alunoId = null,
        ?string $titulo = null,
        ?string $objetivo = null,
        ?string $observacoes = null,
        ?string $dataInicio = null,
        ?string $dataFim = null,
        ?string $status = "ativo"
    ) {
        $this->id = $id;
        $this->alunoId = $alunoId;
        $this->titulo = $titulo;
        $this->objetivo = $objetivo;
        $this->observacoes = $observacoes;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAlunoId(): ?int
    {
        return $this->alunoId;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function getObjetivo(): ?string
    {
        return $this->objetivo;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
    }

    public function getDataInicio(): ?string
    {
        return $this->dataInicio;
    }

    public function getDataFim(): ?string
    {
        return $this->dataFim;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function listar(): array
    {
        $query = "SELECT treinos.*, alunos.nome AS aluno_nome
                  FROM treinos
                  INNER JOIN alunos ON alunos.id = treinos.aluno_id
                  ORDER BY treinos.id DESC";

        return Connect::getInstance()->query($query)->fetchAll();
    }

    public function buscarPorId(int $id): object|bool
    {
        $query = "SELECT treinos.*, alunos.nome AS aluno_nome
                  FROM treinos
                  INNER JOIN alunos ON alunos.id = treinos.aluno_id
                  WHERE treinos.id = :id
                  LIMIT 1";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            return false;
        }

        return $stmt->fetch();
    }

    public function criar(): bool
    {
        $query = "INSERT INTO treinos
                    (aluno_id, titulo, objetivo, observacoes, data_inicio, data_fim, status)
                  VALUES
                    (:aluno_id, :titulo, :objetivo, :observacoes, :data_inicio, :data_fim, :status)";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":aluno_id", $this->alunoId);
        $stmt->bindValue(":titulo", $this->titulo);
        $stmt->bindValue(":objetivo", $this->objetivo);
        $stmt->bindValue(":observacoes", $this->observacoes);
        $stmt->bindValue(":data_inicio", $this->dataInicio);
        $stmt->bindValue(":data_fim", $this->dataFim);
        $stmt->bindValue(":status", $this->status ?: "ativo");
        $stmt->execute();

        if ($stmt->rowCount() !== 1) {
            return false;
        }

        $this->id = (int) Connect::getInstance()->lastInsertId();
        return true;
    }

    public function atualizar(): bool
    {
        $query = "UPDATE treinos
                  SET aluno_id = :aluno_id,
                      titulo = :titulo,
                      objetivo = :objetivo,
                      observacoes = :observacoes,
                      data_inicio = :data_inicio,
                      data_fim = :data_fim,
                      status = :status
                  WHERE id = :id";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":aluno_id", $this->alunoId);
        $stmt->bindValue(":titulo", $this->titulo);
        $stmt->bindValue(":objetivo", $this->objetivo);
        $stmt->bindValue(":observacoes", $this->observacoes);
        $stmt->bindValue(":data_inicio", $this->dataInicio);
        $stmt->bindValue(":data_fim", $this->dataFim);
        $stmt->bindValue(":status", $this->status ?: "ativo");
        $stmt->bindValue(":id", $this->id);

        return $stmt->execute();
    }

    public function excluir(int $id): bool
    {
        $query = "DELETE FROM treinos WHERE id = :id";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
