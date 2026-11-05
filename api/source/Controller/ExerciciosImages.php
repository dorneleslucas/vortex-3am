<?php

namespace Source\Controller;

use RuntimeException;
use Source\Controller\Api;
use Source\Models\ExercicioImage;
use Source\Support\Upload;

class ExerciciosImages extends Api
{
    private function validIds(array $data): ?array
    {
        $exercicioId = filter_var($data['exercicio_id'] ?? null, FILTER_VALIDATE_INT);
        $imageId = isset($data['image_id']) ? filter_var($data['image_id'], FILTER_VALIDATE_INT) : null;
        if (!$exercicioId || $exercicioId < 1 || (isset($data['image_id']) && (!$imageId || $imageId < 1))) return null;
        return [(int) $exercicioId, $imageId ? (int) $imageId : null];
    }

    private function authorized(): bool
    {
        if ($this->authToken(1)) return true;
        $this->call(401, 'unauthorized', 'É necessário autenticar como administrador.', 'error')->back();
        return false;
    }

    public function listar(array $data): void
    {
        if (!$this->authorized()) return;
        $ids = $this->validIds($data);
        if (!$ids) { $this->call(400, 'bad_request', 'ID do exercício inválido.', 'error')->back(); return; }
        $model = new ExercicioImage();
        if (!$model->exercicioExists($ids[0])) { $this->call(404, 'not_found', 'Exercício não encontrado.', 'error')->back(); return; }
        $images = array_map(fn($image) => $this->withPublicUrl($image), $model->listByExercicio($ids[0]));
        $this->call(200, 'success', 'Galeria carregada.', 'success')->back($images);
    }

    public function upload(array $data): void
    {
        if (!$this->authorized()) return;
        $ids = $this->validIds($data);
        if (!$ids) { $this->call(400, 'bad_request', 'ID do exercício inválido.', 'error')->back(); return; }
        $model = new ExercicioImage();
        if (!$model->exercicioExists($ids[0])) { $this->call(404, 'not_found', 'Exercício não encontrado.', 'error')->back(); return; }
        $files = $_FILES['images'] ?? $_FILES['image'] ?? null;
        if (!$files) { $this->call(400, 'bad_request', 'Envie ao menos uma imagem no campo images[].', 'error')->back(); return; }
        $normalized = [];
        if (isset($files['name']) && is_array($files['name'])) {
            foreach ($files['name'] as $i => $name) foreach (['name','type','tmp_name','error','size'] as $key) $normalized[$i][$key] = $files[$key][$i] ?? null;
        } elseif (isset($files['name'])) $normalized[] = $files;
        if (!$normalized || count($normalized) > 10) { $this->call(400, 'bad_request', 'Envie de 1 a 10 imagens por requisição.', 'error')->back(); return; }

        $uploader = new Upload(__DIR__ . '/../../uploads/exercicios');
        $stored = [];
        $saved = [];
        try {
            foreach ($normalized as $index => $file) {
                $path = $uploader->image($file);
                $stored[] = $path;
                $alt = isset($_POST['alt_text']) && is_string($_POST['alt_text']) ? trim($_POST['alt_text']) : null;
                $id = $model->insert($ids[0], $path, $alt !== '' ? $alt : null, $index);
                if ($id === false) throw new RuntimeException('Falha ao salvar os dados da imagem.');
                $saved[] = ['id' => $id, 'exercicio_id' => $ids[0], 'image_path' => $path, 'alt_text' => $alt, 'position' => $index, 'url' => $this->imageUrl($path)];
            }
        } catch (\Throwable $e) {
            foreach ($saved as $row) $model->delete($ids[0], (int) $row['id']);
            foreach ($stored as $path) $this->removeManagedFile($path);
            $this->call(400, 'upload_error', $e->getMessage(), 'error')->back();
            return;
        }
        $this->call(201, 'created', 'Imagem(ns) enviada(s) com sucesso.', 'success')->back($saved);
    }

    public function remove(array $data): void
    {
        if (!$this->authorized()) return;
        $ids = $this->validIds($data);
        if (!$ids || !$ids[1]) { $this->call(400, 'bad_request', 'IDs do exercício e da imagem são obrigatórios.', 'error')->back(); return; }
        $model = new ExercicioImage();
        $image = $model->find($ids[0], $ids[1]);
        if (!$image) { $this->call(404, 'not_found', 'Imagem não encontrada neste exercício.', 'error')->back(); return; }
        if (!$model->delete($ids[0], $ids[1])) { $this->call(500, 'delete_error', 'Não foi possível remover a imagem.', 'error')->back(); return; }
        $this->removeManagedFile($image['image_path']);
        $this->call(200, 'success', 'Imagem removida.', 'success')->back(['id' => $ids[1]]);
    }

    private function withPublicUrl(array $image): array { $image['url'] = $this->imageUrl($image['image_path']); return $image; }
    private function imageUrl(string $path): string { return rtrim(CONF_URL_BASE, '/') . '/api/uploads/exercicios/' . rawurlencode(basename($path)); }
    private function removeManagedFile(string $filename): void
    {
        $basename = basename($filename);
        if ($basename !== $filename || !preg_match('/^[a-f0-9]{32}\\.(jpg|png|webp)$/', $basename)) return;
        $path = dirname(__DIR__, 2) . '/uploads/exercicios/' . $basename;
        if (is_file($path)) @unlink($path);
    }
}
