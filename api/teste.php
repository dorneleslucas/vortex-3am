<?php 
// Carrega o autoload do Composer
require_once 'vendor/autoload.php'; 

// Carrega as configurações (já deve estar no autoload)
use Source\Core\Connect; 

try  { 
    $conn = Source\Core\Connect::getInstance(); 
    echo "✅ Conectou ao banco";  

    // teste uma consulta simples 
    $stmt = $conn->query("SELECT COUNT(*) as total FROM products"); 
    $resultado = $stmt->fetch(); 
    echo "<br> Total de produtos do banco " . $resultado->total; 
} catch (Exception $e) { 
    echo "❌ Erro: " . $e->getMessage(); 

}

?> 