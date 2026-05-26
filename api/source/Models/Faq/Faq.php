<?php

namespace source\Models\Faq;

use PDO;
use Source\Core\Connect;
class Faq
{
    private ?int $id;
    private ?int $faqs_category_id;
    private ?string $question;
    private ?string $answer;

    public function __construct(?int $id = null, ?int $faqs_category_id = null, ?string $question = null, ?float $answer = null)
    {
        $this->id = $id;
        $this->faqs_category_id = $faqs_category_id;
        $this->question = $question;
        $this->answer = $answer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFaqs_category_id(): int
    {
        return $this->faqs_category_id;
    }

    public function setFaqs_category_id(int $faqs_category_id): void
    {
        $this->faqs_category_id = $faqs_category_id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function listAll(): array
    {
        $query = "SELECT f.id, f.question, f.answer,
        c.name AS category_name
        FROM faqs AS f
        JOIN faqs_categories AS c
        ON c.id = f.faqs_category_id
        ORDER BY c.name, f.id";
        $stmt = Connect::getInstance()->query($query);
        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll();
        }
        return [];
    }
    public function listById(int $id): object|bool
    {
        $query = "SELECT * FROM faqs WHERE id = :id";
        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return false;
    }
    public function create(string $question, string $answer, int $faqs_category_id): bool|array
{
    $query = "INSERT INTO faqs (question, answer, faqs_category_id) 
              VALUES (:question, :answer, :cat)";
    
    $stmt = Connect::getInstance()->prepare($query);
    $stmt->bindParam(":question", $question);
    $stmt->bindParam(":answer", $answer);
    $stmt->bindParam(":cat", $faqs_category_id);

    if (!$stmt->execute()) {
        return false;
    }

    $id = Connect::getInstance()->lastInsertId();

    return [
        "id" => (int) $id,
        "question" => $question,
        "answer" => $answer,
        "faqs_category_id" => $faqs_category_id
    ];
}
    public function update(): bool
    {
        $query = "
            UPDATE faqs
            SET question = :question,
                answer = :answer,
                faqs_category_id = :cat
            WHERE id = :id
        ";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":answer", $this->answer);
        $stmt->bindParam(":cat", $this->faqs_category_id);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
    public function softDelete(int $id): bool
    {
        $query = "UPDATE faqs SET active = 0 WHERE id = :id AND active = 1";

        $stmt = Connect::getInstance()->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}