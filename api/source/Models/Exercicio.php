<?php

namespace Source\Models;

use Source\Core\Connect;

class Exercicio
{
    private ?int $id;
    private ?string $nome;
    private ?string $categoria;
    private ?string $descricao;
    private ?int $ativo;

    public function __construct(
        ?int $id = null,
        ?string $nome = null,
        ?string $categoria = null,
        ?string $descricao = null,
        ?int $ativo = 1
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->categoria = $categoria;
        $this->descricao = $descricao;
        $this->ativo = $ativo;
    }

    // Getters e Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(?string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getAtivo(): ?int
    {
        return $this->ativo;
    }

    public function setAtivo(int $ativo): void
    {
        $this->ativo = $ativo;
    }

    // --- QUERIES EXPLÍCITAS ---

    public function listar(): array
    {
        $query = "SELECT * FROM exercicios ORDER BY id DESC";
        return Connect::getInstance()->query($query)->fetchAll();
    }

    public function buscarPorId(int $id): object|bool
    {
        $query = "SELECT * FROM exercicios WHERE id = :id LIMIT 1";
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
        $query = "INSERT INTO exercicios (nome, categoria, descricao, ativo)
                  VALUES (:nome, :categoria, :descricao, :ativo)";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":nome", $this->nome);
        $stmt->bindValue(":categoria", $this->categoria);
        $stmt->bindValue(":descricao", $this->descricao);
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
        $query = "UPDATE exercicios
                  SET nome = :nome,
                      categoria = :categoria,
                      descricao = :descricao,
                      ativo = :ativo
                  WHERE id = :id";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":nome", $this->nome);
        $stmt->bindValue(":categoria", $this->categoria);
        $stmt->bindValue(":descricao", $this->descricao);
        $stmt->bindValue(":ativo", $this->ativo);
        $stmt->bindValue(":id", $this->id);

        return $stmt->execute();
    }

    public function excluir(int $id): bool
    {
        // Soft delete (apenas desativa)
        $query = "DELETE FROM exercicios WHERE id = :id";
        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
        
    }
}