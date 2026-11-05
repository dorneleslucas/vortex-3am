<?php

namespace Source\Support;

use finfo;
use RuntimeException;

class Upload
{
    private const MIME_EXTENSIONS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    public int $maxImageSize = 5242880;
    public array $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(public string $destinationDirectory)
    {
        if (trim($destinationDirectory) === '') throw new RuntimeException('A pasta de destino do upload deve ser informada.');
    }

    public function image(array $upload): string
    {
        foreach (['name','tmp_name','error','size'] as $key) if (!array_key_exists($key, $upload)) throw new RuntimeException('Dados de upload inválidos.');
        if (!is_string($upload['name']) || !is_string($upload['tmp_name']) || !is_int($upload['error']) || !is_int($upload['size'])) throw new RuntimeException('Dados de upload inválidos.');
        if ($upload['error'] !== UPLOAD_ERR_OK) throw new RuntimeException(match ($upload['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'A imagem excede o limite de tamanho.',
            UPLOAD_ERR_NO_FILE => 'Nenhuma imagem foi enviada.',
            UPLOAD_ERR_PARTIAL => 'A imagem foi recebida parcialmente.',
            default => 'Falha ao receber a imagem.'
        });
        if (!is_uploaded_file($upload['tmp_name'])) throw new RuntimeException('Upload não recebido por uma requisição válida.');
        $size = filesize($upload['tmp_name']);
        if ($size === false || $size !== $upload['size'] || $size > $this->maxImageSize) throw new RuntimeException('Imagem inválida ou acima do limite de 5 MB.');
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
        if (!is_string($mime) || !in_array($mime, $this->allowedImageTypes, true)) throw new RuntimeException('Formato não permitido. Use JPEG, PNG ou WebP.');
        $info = getimagesize($upload['tmp_name']);
        if ($info === false || strtolower($info['mime'] ?? '') !== $mime) throw new RuntimeException('O arquivo não contém uma imagem válida.');
        if (!is_dir($this->destinationDirectory) && !mkdir($this->destinationDirectory, 0750, true) && !is_dir($this->destinationDirectory)) throw new RuntimeException('Não foi possível criar a pasta de uploads.');
        if (!is_writable($this->destinationDirectory)) throw new RuntimeException('A pasta de uploads não permite gravação.');
        $filename = bin2hex(random_bytes(16)) . '.' . self::MIME_EXTENSIONS[$mime];
        if (!move_uploaded_file($upload['tmp_name'], $this->destinationDirectory . DIRECTORY_SEPARATOR . $filename)) throw new RuntimeException('Não foi possível armazenar a imagem.');
        return $filename;
    }
}
