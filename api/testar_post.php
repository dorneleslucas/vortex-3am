<?php 
require_once 'vendor/autoload.php'; 

//pega todo o corpo da requisição 
$input = file_get_contents('php://input'); 
echo "Raw input: " . $input . "<br><br>"; 

// decodifica o JSON 
$data = json_decode($input, true); 
echo "Decofificado: <pre>"; 
print_r($data);  
echo "</pre> <br>"; 


//verifica cada campo individualmente 
echo "Verificando campos: <br>";  
echo "name existe? " . (isset($data['name']) ? 'SIM = ' .  $data['name'] : 'NÃO') . "<br>" ; 
echo "price existe? " . (isset($data['price']) ? 'SIM = ' .  $data['price'] : 'NÃO') . "<br>" ; 
echo "category_id existe? " . (isset($data['category_id']) ? 'SIM = ' .  $data['category_id'] : 'NÃO') . "<br>" ; 
echo "categoryId existe? " . (isset($data['categoryId']) ? 'SIM = ' .  $data['categoryId'] : 'NÃO') . "<br>" ; 

?> 