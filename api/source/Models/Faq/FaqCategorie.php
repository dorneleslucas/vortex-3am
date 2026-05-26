<?php

namespace source\Models\Faq;

use PDO;
use Source\Core\Connect;

class FaqCategorie
{
    private ?int $id;
    private ?string $name;

    public function __construct(?int $id = null, ?string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function listAll(): array
    {
        $query = "SELECT faqs_categories.id, faqs_categories.name
                  FROM faqs_categories";
        $stmt = Connect::getInstance()->query($query);
        return $stmt->fetchAll();
    }
    public function listById(int $id): object|bool
    {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return false;
    }
    public function create(string $name): bool|array
    {
        $query = "INSERT INTO faqs_categories (name) VALUES (:name)";
        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":name", $name);
        if (!$stmt->execute()) {
            return false;
        }
        $id = Connect::getInstance()->lastInsertId();
        return [
            "id" => (int) $id,
            "name" => $name
        ];
    }
    public function update(): bool
    {
        $query = "
            UPDATE faqs_categories
            SET name = :name
            WHERE id = :id
        ";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
    public function softDelete(int $id): bool
    {
        $query = "UPDATE faqs_categories SET active = 0 WHERE id = :id AND active = 1";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}