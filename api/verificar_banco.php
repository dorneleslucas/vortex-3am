<?php

require_once 'vendor/autoload.php'; 

use Source\Core\Connect; 

//tenta pegar o nome do banco atual 

$conn = Connect::getInstance(); 
$result = $conn->query("SELECT DATABASE() as db_name"); 
$db = $result->fetch(); 

echo "Banco de dados atual: " . $db->db_name . "<br>"; 

// tenta listar produtos 
$produtos = $conn->query("SELECT COUNT(*) as total FROM products"); 
$total = $produtos->fetch(); 

echo "Total de produtos: " . $total->total . "<br>" ; 

// mostra todas as tabelas 

$tabelas = $conn->query("SHOW TABLES");
echo "<br> Tabelas no banco: <br> "; 
while($tabela = $tabelas->fetch()) { 
    echo "- " . implode(', ' , (array)$tabela) . "<br> "; 
} 

?> 