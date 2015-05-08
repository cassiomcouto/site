<?php
require_once dirname(__FILE__) . "/../class.exportfiletype.php";

class ISC_ADMIN_EXPORTFILETYPE_CUSTOMERS extends ISC_ADMIN_EXPORTFILETYPE
{
	protected $type_name = "customers";
	protected $type_icon = "customer.gif";
	protected $type_idfield = "customerid";
	protected $type_viewlink = "index.php?ToDo=viewCustomers";

	protected $handleaddresses = false;
	protected $handleaddressformfields = false;
	protected $handleformfields = false;

	public function GetFields()
	{
		 $fields = array(
			"customerID"			=> array("dbfield" => "customerid"),
			"customerName"			=> array("dbfield" => "CONCAT(custconfirstname, ' ', custconlastname)"),
			"customerFirstName"		=> array("dbfield" => "custconfirstname"),
			"customerLastName"		=> array("dbfield" => "custconlastname"),
			"customerCompany"		=> array("dbfield" => "custconcompany"),
			"customerEmail"			=> array("dbfield" => "custconemail"),
			"customerPhone"			=> array("dbfield" => "custconphone"),
			"customerNotes"			=> array("dbfield" => "custnotes"),
			"customerCredit"		=> array("dbfield" => "custstorecredit", "format" => "number"),
			"customerGroup"			=> array("dbfield" => "cg.groupname"),
			"customerDateJoined"	=> array("dbfield" => "custdatejoined", "format" => "date"),
			"customerAddresses"		=> array(
											"fields" => array(
															"addressName"			=> array(),
															"addressFirstName"		=> array(),
															"addressLastName"		=> array(),
															"addressCompany"		=> array(),
															"addressLine1"			=> array(),
															"addressLine2"			=> array(),
															"addressSuburb"			=> array(),
															"addressState"			=> array(),
															"addressStateAbbrv"		=> array(),
															"addressPostcode"		=> array(),
															"addressCountry"		=> array(),
															"addressBuilding"		=> array(),
															"addressPhone"			=> array(),
															"addressFormFields"		=> array()
														)
											),
			"customerFormFields"	=> array()

		);

		return $fields;
	}

	protected function PostFieldLoad()
	{
		$fields = $this->fields;

		if ($this->templateid) {
			// is the categories fields used?
			if ($fields['customerAddresses']['used']) {
				// are any sub-fields ticked? let parent handle row output if none are
				$addrfieldsused = false;
				foreach ($fields['customerAddresses']['fields'] as $id => $field) {
					if ($field['used']) {
						$addrfieldsused = true;
						break;
					}
				}

				$this->handleaddresses = $addrfieldsused;

				// address form fields used?
				if ($fields['customerAddresses']['fields']['addressFormFields']['used']) {
					$addressFields = $this->InsertFormFields(FORMFIELDS_FORM_ADDRESS, "addressFormFields", $this->fields['customerAddresses']['fields']);

					// check if form fields were inserted, if they were then addressFormFields won't exist anymore
					if (isset($addressFields['addressFormFields'])) {
						// no form fields, disable the column
						$addressFields['addressFormFields']['used'] = false;
					}
					else {
						$this->handleaddressformfields = true;
					}

					$this->fields['customerAddresses']['fields'] = $addressFields;
				}
			}

			// are form fields used?
			if ($fields['customerFormFields']['used']) {
				// the export fields to insert
				$this->fields = $this->InsertFormFields(FORMFIELDS_FORM_ACCOUNT, "customerFormFields", $this->fields);

				// check if form fields were inserted, if they were then customerFormFields won't exist anymore
				if (isset($this->fields['customerFormFields'])) {
					// no form fields, disable the column
					$this->fields['customerFormFields']['used'] = false;
				}
				else {
					$this->handleformfields = true;
				}
			}
		}
	}

	protected function GetQuery($columns, $where, $vendorid)
	{
		if ($where) {
			$where = " WHERE " . $where;
		}

		$query = "
			SELECT
				DISTINCT
				" . $columns . ",
				customerID AS custID,
				custformsessionid
			FROM
				[|PREFIX|]customers c
				LEFT JOIN [|PREFIX|]customer_groups cg ON cg.customergroupid = c.custgroupid
				LEFT JOIN [|PREFIX|]shipping_addresses sa ON sa.shipcustomerid = c.customerid
			" . $where;

		return $query;
	}

	protected function HandleRow($row)
	{
		if ($this->handleaddresses) {
			// get the addresses for the customer
			$query = "
				SELECT
					CONCAT(shipfirstname, ' ', shiplastname) AS addressName,
					shipfirstname AS addressFirstName,
					shiplastname AS addressLastName,
					shipcompany AS addressCompany,
					shipaddress1 AS addressLine1,
					shipaddress2 AS addressLine2,
					shipcity AS addressSuburb,
					shipstate AS addressState,
					stateabbrv AS addressStateAbbrv,
					shipzip AS addressPostcode,
					shipcountry AS addressCountry,
					shipphone AS addressPhone,
					shipdestination AS addressBuilding,
					shipformsessionid
				FROM
					[|PREFIX|]shipping_addresses sa
					LEFT JOIN [|PREFIX|]country_states cs ON cs.stateid = sa.shipstateid
				WHERE
					shipcustomerid = '" . $row['custID'] . "'";

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if ($GLOBALS['ISC_CLASS_DB']->CountResult($result)) {
				$addresses = array();

				while ($address = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					// create dummy row
					$addr_fields = $this->CreateDummyRow($this->fields['customerAddresses']['fields']);
					foreach ($this->fields['customerAddresses']['fields'] as $id => $field) {
						if ($field['used'] && isset($address[$id])) {
							$addr_fields[$id] = $address[$id];
						}
					}

					// handle address form fields?
					if ($this->handleaddressformfields) {
						$this->LoadFormFieldData(FORMFIELDS_FORM_ADDRESS, 'addressFormFields', $addr_fields, $address['shipformsessionid']);
					}

					// apply any field formatting
					$this->FormatColumns($addr_fields, $this->fields['customerAddresses']['fields']);

					//$new_row = $row;
					$addresses[] = $addr_fields;
				}
			}
			else {
				$address = array();
				foreach ($this->fields['customerAddresses']['fields'] as $id => $field) {
					if ($field['used']) {
						$address[$id] = "";
					}
				}
				$addresses[] = $address;
			}

			$row["customerAddresses"] = $addresses;
		}

		if ($this->handleformfields) {
			// get the form fields with data for this customer
			$this->LoadFormFieldData(FORMFIELDS_FORM_ACCOUNT, "customerFormFields", $row, $row['custformsessionid']);
		}

		return $row;
	}

	public function GetListColumns()
	{
		$columns = array(
			"ID",
			"Name",
			"Email",
			"Phone",
			"Group",
			"Date Joined",
		);

		return $columns;
	}

	public function GetListSortLinks()
	{
		$sortLinks = array(
			"ID" => "customerid",
			"Name" => "custname",
			"Email" => "custconemail",
			"Phone" => "custconphone",
			"Group" => "groupname",
			"Date" => "custdatejoined"
		);

		return $sortLinks;
	}

	public function GetListQuery($where, $vendorid, $sortField, $sortOrder)
	{
		if ($where) {
			$where = "WHERE " . $where;
		}

		$query = "
				SELECT
					DISTINCT customerid,
					CONCAT(custconfirstname, ' ', custconlastname) AS custname,
					custconemail,
					custconphone,
					cg.groupname,
					custdatejoined
				FROM
					[|PREFIX|]customers c
					LEFT JOIN [|PREFIX|]customer_groups cg ON cg.customergroupid = c.custgroupid
					LEFT JOIN [|PREFIX|]shipping_addresses sa ON sa.shipcustomerid = c.customerid
				" . $where . "
				ORDER BY "
					. $sortField . " " . $sortOrder;

		return $query;
	}

	public function GetListCountQuery($where, $vendorid)
	{
		if ($where) {
			$where = "WHERE " . $where;
		}

		$query = "
				SELECT
					COUNT(DISTINCT c.customerid) AS ListCount
				FROM
					[|PREFIX|]customers c
					LEFT JOIN [|PREFIX|]customer_groups cg ON cg.customergroupid = c.custgroupid
					LEFT JOIN [|PREFIX|]shipping_addresses sa ON sa.shipcustomerid = c.customerid
				" . $where;

		return $query;
	}

	public function GetListRow($row)
	{
		$new_row['ID'] = $row['customerid'];
		$new_row['Name'] = isc_html_escape($row['custname']);
		$new_row['Email'] = $row['custconemail'];
		$new_row['Phone'] = $row['custconphone'];
		$new_row['Group'] = $row['groupname'];
		$new_row['Date Joined'] = date("jS M Y", $row['custdatejoined']);

		return $new_row;
	}

	protected function BuildWhereFromFields($search_fields)
	{
		$class = GetClass('ISC_ADMIN_CUSTOMERS');

		$res = $class->BuildWhereFromVars($search_fields);
		$where = "1 = 1 " . $res['query'];
		// strip AND from beginning and end of statement
		$where = preg_replace("/^( ?AND )?|( AND ?)?$/i", "", $where);

		return $where;
	}

	public function HasPermission()
	{
		return $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Export_Customers);
	}
}