<?php

namespace Source\Controller;

use RuntimeException;
use Source\Models\ProductImage;
use Source\Support\Upload;

class ProductsImages extends Api
{
    private function authorize(): bool
    {
        if ($this->authToken(1)) return true;
        $this->call(401, 'unauthorized', 'É necessário autenticar como administrador.', 'error')->back();
        return false;
    }

    private function productId(array $data): ?int
    {
        $id = filter_var($data['product_id'] ?? null, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? (int) $id : null;
    }

    private function imageId(array $data): ?int
    {
        $id = filter_var($data['image_id'] ?? null, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? (int) $id : null;
    }

    public function listar(array $data): void
    {
        if (!$this->authorize()) return;
        $productId = $this->productId($data);
        if (!$productId) { $this->call(400, 'bad_request', 'ID do produto inválido.', 'error')->back(); return; }

        $model = new ProductImage();
        if (!$model->productExists($productId)) { $this->call(404, 'not_found', 'Produto não encontrado.', 'error')->back(); return; }

        $images = array_map(fn(array $image) => $this->withPublicUrl($image), $model->listByProduct($productId));
        $this->call(200, 'success', 'Galeria carregada.', 'success')->back($images);
    }

    public function upload(array $data): void
    {
        if (!$this->authorize()) return;
        $productId = $this->productId($data);
        if (!$productId) { $this->call(400, 'bad_request', 'ID do produto inválido.', 'error')->back(); return; }

        $model = new ProductImage();
        if (!$model->productExists($productId)) { $this->call(404, 'not_found', 'Produto não encontrado.', 'error')->back(); return; }

        $uploads = $_FILES['images'] ?? $_FILES['image'] ?? null;
        if (!$uploads) { $this->call(400, 'bad_request', 'Envie imagens no campo images[].', 'error')->back(); return; }
        $files = $this->normalizeFiles($uploads);
        if (!$files || count($files) > 10) { $this->call(400, 'bad_request', 'Envie de 1 a 10 imagens por requisição.', 'error')->back(); return; }

        $altText = $_POST['alt_text'] ?? null;
        if ($altText !== null && (!is_string($altText) || mb_strlen($altText) > 255)) {
            $this->call(400, 'bad_request', 'O texto alternativo deve ter até 255 caracteres.', 'error')->back(); return;
        }
        $altText = is_string($altText) && trim($altText) !== '' ? trim($altText) : null;

        $uploader = new Upload(__DIR__ . '/../../uploads/products');
        $stored = [];
        $saved = [];
        $failureCode = 400;
        try {
            foreach ($files as $position => $file) {
                $path = $uploader->image($file);
                $stored[] = $path;
                $id = $model->insert($productId, $path, $altText, $position);
                if ($id === false) {
                    $failureCode = 500;
                    throw new RuntimeException('Não foi possível salvar os dados da imagem.');
                }
                $saved[] = [
                    'id' => $id,
                    'product_id' => $productId,
                    'image_path' => $path,
                    'alt_text' => $altText,
                    'position' => $position,
                    'url' => $this->imageUrl($path),
                ];
            }
        } catch (\Throwable $e) {
            foreach ($saved as $image) $model->delete($productId, $image['id']);
            foreach ($stored as $path) $this->removeManagedFile($path);
            $this->call($failureCode, $failureCode === 500 ? 'persistence_error' : 'upload_error', $e->getMessage(), 'error')->back();
            return;
        }

        $this->call(201, 'created', 'Imagem(ns) enviada(s) com sucesso.', 'success')->back($saved);
    }

    public function remove(array $data): void
    {
        if (!$this->authorize()) return;
        $productId = $this->productId($data);
        $imageId = $this->imageId($data);
        if (!$productId || !$imageId) { $this->call(400, 'bad_request', 'IDs do produto e da imagem são obrigatórios.', 'error')->back(); return; }

        $model = new ProductImage();
        $image = $model->find($productId, $imageId);
        if (!$image) { $this->call(404, 'not_found', 'Imagem não encontrada neste produto.', 'error')->back(); return; }
        if (!$model->delete($productId, $imageId)) { $this->call(500, 'delete_error', 'Não foi possível remover a imagem.', 'error')->back(); return; }

        $this->removeManagedFile($image['image_path']);
        $this->call(200, 'success', 'Imagem removida.', 'success')->back(['id' => $imageId]);
    }

    private function normalizeFiles(array $uploads): array
    {
        if (!isset($uploads['name'])) return [];
        if (!is_array($uploads['name'])) return [$uploads];
        $files = [];
        foreach (array_keys($uploads['name']) as $index) {
            $files[] = [
                'name' => $uploads['name'][$index] ?? null,
                'type' => $uploads['type'][$index] ?? null,
                'tmp_name' => $uploads['tmp_name'][$index] ?? null,
                'error' => $uploads['error'][$index] ?? null,
                'size' => $uploads['size'][$index] ?? null,
            ];
        }
        return $files;
    }

    private function withPublicUrl(array $image): array
    {
        $image['url'] = $this->imageUrl($image['image_path']);
        return $image;
    }

    private function imageUrl(string $filename): string
    {
        return rtrim(CONF_URL_BASE, '/') . '/api/uploads/products/' . rawurlencode(basename($filename));
    }

    private function removeManagedFile(string $filename): void
    {
        $basename = basename($filename);
        if ($basename !== $filename || !preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $basename)) return;
        $path = dirname(__DIR__, 2) . '/uploads/products/' . $basename;
        if (is_file($path)) @unlink($path);
    }
}
