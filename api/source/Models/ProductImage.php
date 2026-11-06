<?php

namespace Source\Models;

use PDO;
use PDOException;
use Source\Core\Connect;

class ProductImage
{
    private ?string $errorMessage = null;

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function productExists(int $productId): bool
    {
        $stmt = Connect::getInstance()->prepare('SELECT id FROM products WHERE id = :id AND active = 1 LIMIT 1');
        $stmt->execute([':id' => $productId]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(int $productId, string $path, ?string $altText, int $position): int|false
    {
        try {
            $stmt = Connect::getInstance()->prepare(
                'INSERT INTO product_images (product_id, image_path, alt_text, position) VALUES (:product_id, :image_path, :alt_text, :position)'
            );
            $stmt->execute([
                ':product_id' => $productId,
                ':image_path' => $path,
                ':alt_text' => $altText,
                ':position' => $position,
            ]);
            return (int) Connect::getInstance()->lastInsertId();
        } catch (PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function listByProduct(int $productId): array
    {
        $stmt = Connect::getInstance()->prepare(
            'SELECT id, product_id, image_path, alt_text, position, created_at FROM product_images WHERE product_id = :product_id ORDER BY position, id'
        );
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $productId, int $imageId): ?array
    {
        $stmt = Connect::getInstance()->prepare(
            'SELECT id, product_id, image_path, alt_text FROM product_images WHERE product_id = :product_id AND id = :id LIMIT 1'
        );
        $stmt->execute([':product_id' => $productId, ':id' => $imageId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        return $image ?: null;
    }

    public function delete(int $productId, int $imageId): bool
    {
        $stmt = Connect::getInstance()->prepare(
            'DELETE FROM product_images WHERE product_id = :product_id AND id = :id'
        );
        $stmt->execute([':product_id' => $productId, ':id' => $imageId]);
        return $stmt->rowCount() === 1;
    }

    public function deleteAllForProduct(int $productId): array
    {
        $images = $this->listByProduct($productId);
        $stmt = Connect::getInstance()->prepare('DELETE FROM product_images WHERE product_id = :product_id');
        $stmt->execute([':product_id' => $productId]);
        return $images;
    }
}
