<?php

namespace Source\Models\Faq;

use PDO;
use Source\Core\Connect;
use Source\Core\Model;
class Faq extends Model
{
    private ?int $id;
    private ?int $faqsCategoryId;
    private ?string $question;
    private ?string $answer;
    private ?int $active;
    private ?string $createdAt;
    private ?string $userId;

    public function __construct(?int $id = null, ?int $faqsCategoryId = null, ?string $question = null, ?string $answer = null, ?int $active = 1, ?string $createdAt = null, ?string $userId = null)
    {
        $this->id = $id;
        $this->faqsCategoryId = $faqsCategoryId;
        $this->question = $question;
        $this->answer = $answer;
        $this->active = $active;
        $this->createdAt = $createdAt;
        $this->userId = $userId;
        
        $this->table = 'faqs'; // nome da tabela do banco
        $this->primaryKey = 'id'; // nome da chave primária da tabela
        $this->fillable = ['faqsCategoryId', 'question', 'answer', 'active', 'createdAt', 'userId']; // camelCase
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getFaqsCategoryId(): ?int
    {
        return $this->faqsCategoryId;
    }

    public function setFaqsCategoryId(int $faqsCategoryId): void
    {
        $this->faqsCategoryId = $faqsCategoryId;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }
    public function getActive(): ?string
    {
        return $this->active;
    }
    public function setActive(int $active): void
    {
        $this->active = $active;
    }
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getUserId(): ?string
    {
        return $this->userId;
    }
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
    public function selectFaq(): array
    {
        try {
            $query = "
            SELECT 
                f.id,
                f.question,
                f.answer,
                f.created_at,
                c.name AS category_name,
                u.name AS user_name
            FROM faqs f
            JOIN faqs_categories c ON c.id = f.faqs_category_id
            LEFT JOIN users u ON u.id = f.user_id
            ORDER BY f.id DESC;
        ";

            $stmt = Connect::getInstance()->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return [];
        }
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
            $stmt->bindParam(":cat", $this->faqsCategoryId);
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