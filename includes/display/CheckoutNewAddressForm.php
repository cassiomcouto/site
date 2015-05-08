<?php
require_once(ISC_BASE_PATH . '/lib/addressvalidation.php');

class ISC_CHECKOUTNEWADDRESSFORM_PANEL extends PANEL
{
	public function SetPanelSettings()
	{
		// this panel should only be shown for guests entering an address
		if(CustomerIsSignedIn()) {
			$this->DontDisplay = true;
			return;
		}

		$fields = "";

		// display email address for choose billing address step only
		if ($GLOBALS['ShippingFormAction'] == "save_biller") {
			// load the email address field
			$GLOBALS['ISC_CLASS_FORM']->addFormFieldUsed($GLOBALS['ISC_CLASS_FORM']->getFormField(FORMFIELDS_FORM_ACCOUNT, '1', '', true));

			// load html for email field
			$fields = $GLOBALS['ISC_CLASS_FORM']->loadFormField(FORMFIELDS_FORM_ACCOUNT, '1');

			$GLOBALS['CheckEmail'] = 'true';
		}

		// generate fields
		$fields .= buildFieldData();
		$GLOBALS['ShipCustomFields'] = $fields;
		$GLOBALS['AddressFormFieldID'] = FORMFIELDS_FORM_ADDRESS;
		/**
		 * Load up any form field JS event data and any validation lang variables
		 */
		$GLOBALS['FormFieldRequiredJS'] = $GLOBALS['ISC_CLASS_FORM']->buildRequiredJS();
	}
}