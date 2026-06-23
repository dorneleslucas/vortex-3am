<?php

namespace Source\Models;

use Source\Core\Connect;

class Aluno
{
    private ?int $id;
    private ?int $personalId;
    private ?string $nome;
    private ?string $email;
    private ?string $telefone;
    private ?string $objetivo;
    private ?int $ativo;

    public function __construct(
        ?int $id = null,
        ?int $personalId = null,
        ?string $nome = null,
        ?string $email = null,
        ?string $telefone = null,
        ?string $objetivo = null,
        ?int $ativo = 1
    ) {
        $this->id = $id;
        $this->personalId = $personalId;
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->objetivo = $objetivo;
        $this->ativo = $ativo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPersonalId(): ?int
    {
        return $this->personalId;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function getObjetivo(): ?string
    {
        return $this->objetivo;
    }

    public function getAtivo(): ?int
    {
        return $this->ativo;
    }

    public function listar(): array
    {
        $query = "SELECT alunos.*, users.name AS personal_nome
                  FROM alunos
                  INNER JOIN users ON users.id = alunos.personal_id
                  ORDER BY alunos.id DESC";

        return Connect::getInstance()->query($query)->fetchAll();
    }

    public function buscarPorId(int $id): object|bool
    {
        $query = "SELECT alunos.*, users.name AS personal_nome
                  FROM alunos
                  INNER JOIN users ON users.id = alunos.personal_id
                  WHERE alunos.id = :id
                  LIMIT 1";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            return false;
        }

        return $stmt->fetch();
    }

    public function buscarPorPersonal(int $personalId): array
    {
        $query = "SELECT alunos.*
                  FROM alunos
                  WHERE alunos.personal_id = :personal_id
                  ORDER BY alunos.id DESC";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":personal_id", $personalId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function criar(): bool
    {
        $query = "INSERT INTO alunos
                    (personal_id, nome, email, telefone, objetivo, ativo)
                  VALUES
                    (:personal_id, :nome, :email, :telefone, :objetivo, :ativo)";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":personal_id", $this->personalId);
        $stmt->bindValue(":nome", $this->nome);
        $stmt->bindValue(":email", $this->email);
        $stmt->bindValue(":telefone", $this->telefone);
        $stmt->bindValue(":objetivo", $this->objetivo);
        $stmt->bindValue(":ativo", $this->ativo ?? 1);
        $stmt->execute();

        if ($stmt->rowCount() !== 1) {
            return false;
        }

        $this->id = (int) Connect::getInstance()->lastInsertId();
        return true;
    }

    public function atualizar(): bool
    {
        $query = "UPDATE alunos
                  SET personal_id = :personal_id,
                      nome = :nome,
                      email = :email,
                      telefone = :telefone,
                      objetivo = :objetivo,
                      ativo = :ativo
                  WHERE id = :id";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":personal_id", $this->personalId);
        $stmt->bindValue(":nome", $this->nome);
        $stmt->bindValue(":email", $this->email);
        $stmt->bindValue(":telefone", $this->telefone);
        $stmt->bindValue(":objetivo", $this->objetivo);
        $stmt->bindValue(":ativo", $this->ativo);
        $stmt->bindValue(":id", $this->id);

        return $stmt->execute();
    }

    public function excluir(int $id): bool
    {
        $query = "DELETE FROM alunos WHERE id = :id";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
