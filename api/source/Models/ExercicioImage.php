<?php

namespace Source\Models;

use PDO;
use PDOException;
use Source\Core\Connect;

class ExercicioImage
{
    private ?string $errorMessage = null;

    public function getErrorMessage(): ?string { return $this->errorMessage; }

    public function exercicioExists(int $exercicioId): bool
    {
        $stmt = Connect::getInstance()->prepare('SELECT id FROM exercicios WHERE id = :id AND ativo = 1 LIMIT 1');
        $stmt->execute([':id' => $exercicioId]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(int $exercicioId, string $path, ?string $altText, int $position): int|false
    {
        try {
            $stmt = Connect::getInstance()->prepare('INSERT INTO exercicio_images (exercicio_id, image_path, alt_text, position) VALUES (:exercicio_id, :image_path, :alt_text, :position)');
            $stmt->execute([':exercicio_id' => $exercicioId, ':image_path' => $path, ':alt_text' => $altText, ':position' => $position]);
            return (int) Connect::getInstance()->lastInsertId();
        } catch (PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function listByExercicio(int $exercicioId): array
    {
        $stmt = Connect::getInstance()->prepare('SELECT id, exercicio_id, image_path, alt_text, position, created_at FROM exercicio_images WHERE exercicio_id = :exercicio_id ORDER BY position, id');
        $stmt->execute([':exercicio_id' => $exercicioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $exercicioId, int $imageId): ?array
    {
        $stmt = Connect::getInstance()->prepare('SELECT id, exercicio_id, image_path, alt_text FROM exercicio_images WHERE exercicio_id = :exercicio_id AND id = :id LIMIT 1');
        $stmt->execute([':exercicio_id' => $exercicioId, ':id' => $imageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function delete(int $exercicioId, int $imageId): bool
    {
        $stmt = Connect::getInstance()->prepare('DELETE FROM exercicio_images WHERE exercicio_id = :exercicio_id AND id = :id');
        $stmt->execute([':exercicio_id' => $exercicioId, ':id' => $imageId]);
        return $stmt->rowCount() === 1;
    }

}
