<?php

namespace source\Models\Product;

use Source\Core\Connect;

class ProductCategory
{
    private ?int $id;
    private ?string $name;

    public function __construct(?int $id = null, ?string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function listAll(): array
    {
        $query = "SELECT * FROM products_categories";
        $stmt = Connect::getInstance()->query($query);
        return $stmt->fetchAll();
    }
    public function categoryFindById(int $id): object|bool
    {
        $query = "SELECT * FROM products_categories WHERE id = :id";
        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return false;
    }
    public function create(): bool
    {
        $query = "INSERT INTO products_categories (name) VALUES (:name)";
        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $this->id = Connect::getInstance()->lastInsertId();
            return true;
        }

        return false;
    }
}