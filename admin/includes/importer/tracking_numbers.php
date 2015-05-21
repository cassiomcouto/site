<?php
require_once(dirname(__FILE__) . "/../classes/class.batch.importer.php");

class ISC_BATCH_IMPORTER_TRACKING_NUMBERS extends ISC_BATCH_IMPORTER_BASE
{
	/**
	 * @var string The type of content we're importing. Should be lower case and correspond with template and language variable names.
	 */
	protected $type = "ordertrackingnumbers";

	protected $_RequiredFields = array(
		"ordernumber",
		"ordertrackingnumber"
	);

	public function __construct()
	{
		$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('batch.importer');

		/**
		 * @var array Array of importable fields and their friendly names.
		 */
		$this->_ImportFields = array(
			"ordernumber" => GetLang('OrderNumber'),
			"ordertrackingnumber" => GetLang('OrdTrackingNo'),
		);

		parent::__construct();
	}

	/**
	 * Custom step 1 code specific to tracking number importing. Calls the parent ImportStep1 funciton.
	 */
	protected function _ImportStep1($MsgDesc="", $MsgStatus="")
	{
		if ($MsgDesc != "" && !isset($GLOBALS['Message'])) {
			$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
		}
		// Set up generic import options
		parent::_ImportStep1();
	}

	/**
	 * Custom step 2 code specific to product importing. Calls the parent ImportStep2 funciton.
	 */
	protected function _ImportStep2($MsgDesc="", $MsgStatus="")
	{
		// Set up generic import options
		if ($MsgDesc != "") {
			$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
		}
		$this->ImportSession['updateOrderStatus'] = $_POST['updateOrderStatus'];
		parent::_ImportStep2();
	}

	/**
	 * Imports an tracking numbers in to the database.
	 *
	 * @param array Array of record data
	 */
	protected function _ImportRecord($record)
	{
		if(trim($record['ordernumber']) == "") {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportMissingOrderNumber');
			return;
		}

		$record['ordertrackingnumber'] = trim($record['ordertrackingnumber']);
		if($record['ordertrackingnumber'] == "") {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportMissingTrackingNumber');
			return;
		}

		if(isc_strlen($record['ordertrackingnumber']) > 100) {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportTrackingNumberTooLong');
			return;
		}

		// Does the order number exist in the database?
		$query = "SELECT orderid, ordtrackingno, ordvendorid FROM [|PREFIX|]orders WHERE orderid='".(int)$record['ordernumber']."'";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$order = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if(!$order['orderid']) {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportInvalidOrderNumber');
			return;
		}

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() != $order['ordvendorid']) {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportInvalidOrderNumber');
			return;
		}

		// Does this order already have a tracking number?
		if($order['ordtrackingno']) {
			// Overriding existing tracking number
			if(isset($this->ImportSession['OverrideDuplicates']) && $this->ImportSession['OverrideDuplicates'] == 1) {
				$this->ImportSession['Results']['Updates'][] = $record['ordernumber']." ".$record['ordertrackingnumber'];
			}
			else {
				$this->ImportSession['Results']['Duplicates'][] = $record['ordernumber']." ".$record['ordertrackingnumber'];
				return;
			}
		}

		$orderData = array(
			"ordtrackingno" => $record['ordertrackingnumber'],
		);

		if (isset($this->ImportSession['updateOrderStatus']) && $this->ImportSession['updateOrderStatus']!=0) {
			$orderData['ordstatus'] = (int) $this->ImportSession['updateOrderStatus'];
		}
		if ($record['ordernumber'] > 0) {
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("orders", $orderData, "orderid='".$order['orderid']."'");
			++$this->ImportSession['Results']['SuccessCount'];
		} else {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportInvalidOrderNumber');
			return;
		}
	}
}