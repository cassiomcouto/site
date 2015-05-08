<?php

	CLASS ISC_HEADER_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{
			// Are we using a text or image-based logo?
			$GLOBALS['HeaderLogo'] = FetchHeaderLogo();
			
			// Show the login/logout link as required
			if(!isset($GLOBALS['LoginOrLogoutText'])) {
				if(CustomerIsSignedIn()) {
	
					// If they're a customer, set their name so it's available in the templates
					$c = GetClass('ISC_CUSTOMER');
					$customerData = $c->GetCustomerDataByToken();
					$GLOBALS['CurrentCustomerFirstName'] = isc_html_escape($customerData['custconfirstname']);
					$GLOBALS['CurrentCustomerLastName'] = isc_html_escape($customerData['custconlastname']);
					$GLOBALS['CurrentCustomerEmail'] = isc_html_escape($customerData['custconemail']);
	
					$GLOBALS['LoginOrLogoutLink'] = "login.php?action=logout";
					//$GLOBALS['CampoLogin'] = printf(GetLang('LogoutLink'), $GLOBALS['ShopPathNormal']);
					
					//$GLOBALS['CampoLogin'] = ''
					
					$GLOBALS['ImagemDivTopoLogin'] = 'logado.png';
					
					$GLOBALS['LoginOrLogoutText'] = '<div class="ClienteLogado">Ol&aacute; <b>'.$GLOBALS['CurrentCustomerFirstName'].' .</b> </a>
					 ( <a href="'.$GLOBALS['ShopPathNormal'].'/areadecliente.php?action=order_status">Meus Pedidos</a>
					<a href="'.$GLOBALS['ShopPathNormal'].'/areadecliente.php?action=account_details">Meus Dados</a>
					<a href="'.$GLOBALS['ShopPathNormal'].'/login.php?action=logout">Sair</a> ) </div>
					
				
					';
				}
				else {
					
						$GLOBALS['ImagemDivTopoLogin'] = 'Login.png';
					
						$endereco = $_SERVER ['REQUEST_URI'];
						$explonome = explode('.',$endereco);

						$explonomex = explode('/',$endereco);
						$number = count($explonomex)-1;


					if($explonomex[$number]=="checkout.php" or $explonomex[$number]=="checkout" or $explonomex[$number]=="login.php" or $explonomex[$number]=="login"){
						$GLOBALS['CampoLogin'] = 	"";
					}else{
					
					$GLOBALS['LoginOrLogoutText'] = '<form class="LoginTopo" action="'.$GLOBALS['ShopPathNormal'].'/login.php?action=check_login" method="post" onsubmit="return check_login_form()"><input type="text" id="LoginX" title="Seu E-mail" class="InputTopo User" name="login_email" id="login_email" /><input type="password" class="InputTopo Senha" name="login_pass" id="login_pass" /><input  type="submit" class="InputTopo Botao" value="" style="padding:2px 10px 2px 10px" /></form>
					
			';
					$loginLinkFunction = '';
					$createAccountLinkFunction = '';
					$GLOBALS['OptimizerLinkScript'] = $this -> insertOptimizerLinkScript();
					if($GLOBALS['OptimizerLinkScript'] != '') {
						$loginLinkFunction = "gwoTracker._link(\"".$GLOBALS['ShopPathSSL']."/login.php?tk=".session_id()."\"); return false;";
						$createAccountLinkFunction = "gwoTracker._link(\"".$GLOBALS['ShopPathSSL']."/login.php?action=create_account&tk=".session_id()."\"); return false;";
	
					}
					}
					
					// If they're a guest, set their name to 'Guest'
					$GLOBALS['USUARIO'] = GetLang('Guest');
					$GLOBALS['CurrentCustomerLastName'] = $GLOBALS['CurrentCustomerEmail'] = '';
					$GLOBALS['LoginOrLogoutText____________DESABILITADO']="<strong>Olá visitante</strong>, <u>(<a href='".$GLOBALS['ShopPathSSL']."/login.php'>Clique aqui para entrar</a> ou <a href='".$GLOBALS['ShopPathSSL']."/login.php?action=create_account&tk=".session_id()."'>crie uma nova conta</a>)</u>";
	/*
					$GLOBALS['LoginOrLogoutLink'] = "login.php";
					$GLOBALS['LoginOrLogoutText'] = sprintf(GetLang('SignInOrCreateAccount'), $GLOBALS['ShopPath'], $loginLinkFunction, $GLOBALS['ShopPath'], $createAccountLinkFunction);
					*/
				}
		}            

		// Display our currency flags. Has been disabled for the time being. Theory being that this will include the whole locale (text aswell)
		$GLOBALS['CurrencyFlags'] = "";
	}
	

	

	public function insertOptimizerLinkScript()
	{

		// if it's not using shared ssl,  do nothing
		if(GetConfig('UseSSL') != 2) {
			return;
		}

		$trackingScript = '';

		//we are here, means the store is using a shared ssl, the checkout page is on a different domain, check if and of the storewide test using finish order page as conversion page, if so, this test is a cross domain test, we need to modify the process to checkout link on the cart page so it pass the user cookies to the checkout page.
		$optimizerStorewide = GetClass('ISC_OPTIMIZER');
		$secondDomainPages = array('AccountCreated');
		$linkScript = $optimizerStorewide->getLinkScriptForConversionPage($secondDomainPages);

		//No storewide optimizer test is using finish order page as conversion page. we need to check the product/category/page based tests.
		if($linkScript == '') {
			$optimizerPerPage = GetClass('ISC_OPTIMIZER_PERPAGE');
			$linkScript = $optimizerPerPage->getLinkScriptForConversionPage($secondDomainPages);
		}

		return $linkScript;
	}
}
	
	?>