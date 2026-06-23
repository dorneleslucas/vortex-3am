<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/source/Config/Config.php';
require __DIR__ . '/source/Core/Connect.php';

use Source\Models\Aluno;

header('Content-Type: application/json; charset=UTF-8');

try {
    $aluno = new Aluno();
    $resultado = $aluno->listar();

    echo json_encode([
        'success' => true,
        'total' => count($resultado),
        'data' => $resultado
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
