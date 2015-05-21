<?php
$atual = $_GET['atual'];
$foto = $_GET['foto'];
//create the directory if doesn't exists (should have write permissons)
if(!is_dir("../../../banners/".$atual."/")) mkdir("../../../banners/".$atual."/", 0755); 
//move the uploaded file
move_uploaded_file($_FILES['Filedata']['tmp_name'], "../../../banners/".$atual."/".$foto);
chmod("../../../banners/".$atual."/".$foto, 0777);
?>