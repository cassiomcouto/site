<?php

	CLASS ISC_HEADERCLEAN_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{
			$GLOBALS['HeaderLogo'] = FetchHeaderLogo();
			
			/*
			
			opcao somente sem URL amigaveis 
			
			*/
			/*	
			$arquivo = $_SERVER['REQUEST_URI'];
			$final = basename($arquivo);
			$quebra = explode("?",$final);
			
			if($quebra[0]=="login.php"){
			$GLOBALS['Processo']="none";
			$GLOBALS['User']="block";
			}
			
			if($quebra[0]=="cart.php"){
			$GLOBALS['Processo']="block";
			$GLOBALS['User']="none";
			$GLOBALS['Icone1'] = "On";
			$GLOBALS['Icone2'] = "Off";
			$GLOBALS['Icone3'] = "Off";
			}
			
			if($quebra[0]=="checkout.php"){
			$GLOBALS['Processo']="block";
			$GLOBALS['User']="none";
			$GLOBALS['Icone1'] = "Off";
			$GLOBALS['Icone2'] = "On";
			$GLOBALS['Icone3'] = "Off";
			}
			
			
			if($quebra[0]=="finishorder.php"){
			$GLOBALS['Processo']="block";
			$GLOBALS['User']="none";
			$GLOBALS['Icone1'] = "Off";
			$GLOBALS['Icone2'] = "Off";
			$GLOBALS['Icone3'] = "On";
			}
			
			*/
			
			
						$endereco = $_SERVER ['REQUEST_URI'];
						$explonome = explode('.',$endereco);

						$explonomex = explode('/',$endereco);
						$number = count($explonomex)-1;
						
						$final = basename($endereco);
						$quebra = explode("?",$final);
						
						
						if($quebra[0]=="login.php" or $explonomex[$number]=="login"){
						$GLOBALS['Processo']="none";
						$GLOBALS['User']="block";
						}
						
						if($quebra[0]=="compras.php" or $explonomex[$number]=="cart"){
						$GLOBALS['Processo']="block";
						$GLOBALS['User']="none";
						$GLOBALS['Icone1'] = "On";
						$GLOBALS['Icone2'] = "Off";
						$GLOBALS['Icone3'] = "Off";
						}
						
						if($quebra[0]=="concluir.php" or $explonomex[$number]=="checkout"){
						$GLOBALS['Processo']="block";
						$GLOBALS['User']="none";
						$GLOBALS['Icone1'] = "Off";
						$GLOBALS['Icone2'] = "On";
						$GLOBALS['Icone3'] = "Off";
						}
						
						
						if($quebra[0]=="formadepagamento.php" or $explonomex[$number]=="finishorder"){
						$GLOBALS['Processo']="block";
						$GLOBALS['User']="none";
						$GLOBALS['Icone1'] = "Off";
						$GLOBALS['Icone2'] = "Off";
						$GLOBALS['Icone3'] = "On";
						}
                                                   
			
		}
		
		
	}
	
?>