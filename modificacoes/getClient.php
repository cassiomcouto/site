<?php
ini_set("display_errors", 0);
$cep = $_GET['getClientId'];
if(!empty($cep)) {
$url = "http://cep.republicavirtual.com.br/web_cep.php?cep=".$cep."&formato=xml";

$end = simplexml_load_file($url);

$id = $end->xpath('resultado');
$estado = $end->xpath('uf');
$cidade = $end->xpath('cidade');
$bairro = $end->xpath('bairro');
$tipo = $end->xpath('tipo_logradouro');
$log = $end->xpath('logradouro');

if($id[0]>=1){  
    echo "formObj.FormField_8.value = '".$tipo[0]." ".$log[0]."';\n";    
    echo "formObj.FormField_9.value = '".$bairro[0]."';\n";    
    echo "formObj.FormField_10.value = '".$cidade[0]."';\n"; 
    //echo "formObj.FormField_11.value = 'Brasil';\n";   	
    echo "formObj.FormField_12.value = '".$estado[0]."';\n";    
}else{
    echo "formObj.FormField_8.value = ' ';\n";    
    echo "formObj.FormField_9.value = ' ';\n";    
    echo "formObj.FormField_10.value = ' ';\n";    
    echo "formObj.FormField_12.value = ' ';\n";     
} 
}else{
    echo "formObj.FormField_8.value = ' ';\n";    
    echo "formObj.FormField_9.value = ' ';\n";    
    echo "formObj.FormField_10.value = ' ';\n";    
    echo "formObj.FormField_12.value = ' ';\n";    
}
?> 