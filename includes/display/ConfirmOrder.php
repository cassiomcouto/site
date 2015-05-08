<?php
CLASS ISC_CONFIRMORDER_PANEL extends PANEL
{
	public function SetPanelSettings()
	{
		// How did they get here without a billing address?
		if (!isset($_SESSION['CHECKOUT']['BILLING_ADDRESS'])) {
			ob_end_clean();
			header(sprintf("location:%s/concluir.php?action=choose_billing_address", $GLOBALS['ShopPath']));
			die();
		}
		$GLOBALS['ISC_CLASS_CHECKOUT'] = GetClass('ISC_CHECKOUT');
		$GLOBALS['ISC_CLASS_CHECKOUT']->BuildOrderConfirmation();
	}
}