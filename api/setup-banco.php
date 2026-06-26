<?php
require __DIR__ . '/vendor/autoload.php';

use Source\Core\Connect;

try {
    $pdo = Connect::getInstance();

    // Adicionar tipo Aluno
    $pdo->exec("INSERT INTO users_types (id, name) VALUES (3, 'Aluno') ON DUPLICATE KEY UPDATE name='Aluno'");

    // Senhas hash (correspondem a "123456")
    $password = password_hash('123456', PASSWORD_DEFAULT);

    // Atualizar senhas dos usuários existentes
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$password, 'rafael@vortex.com']);
    $stmt->execute([$password, 'admin@vortex.com']);

    // Garantir que admin existe
    $stmt = $pdo->prepare("INSERT INTO users (type_id, name, email, password, active) VALUES (1, 'Admin', 'admin@vortex.com', ?, 1) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute([$password, $password]);

    // Garantir que Rafael (personal) existe
    $stmt = $pdo->prepare("INSERT INTO users (type_id, name, email, password, active) VALUES (2, 'Rafael Personal', 'rafael@vortex.com', ?, 1) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute([$password, $password]);

    echo json_encode([
        'success' => true,
        'message' => 'Banco atualizado com sucesso',
        'users' => $pdo->query("SELECT id, name, email, type_id FROM users")->fetchAll(PDO::FETCH_OBJ),
        'types' => $pdo->query("SELECT * FROM users_types")->fetchAll(PDO::FETCH_OBJ)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
