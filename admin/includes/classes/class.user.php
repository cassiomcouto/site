<?php
class ISC_ADMIN_USER
{
	/**
	 * @var array An array of permissions that vendors are allowed to adjust.
	 */
	private $vendorOnlyPermissions = array(
		AUTH_Manage_Orders,
		AUTH_Edit_Orders,
		AUTH_Add_Orders,
		AUTH_Manage_Returns,
		AUTH_Manage_Customers,
		AUTH_Manage_Reviews,
		AUTH_Edit_Reviews,
		AUTH_Delete_Reviews,
		AUTH_Approve_Reviews,
		AUTH_Manage_Pages,
		AUTH_Add_Pages,
		AUTH_Edit_Pages,
		AUTH_Delete_Pages,
		AUTH_Manage_Products,
		AUTH_Create_Product,
		AUTH_Edit_Products,
		AUTH_Delete_Products,
		AUTH_Export_Products,
		AUTH_Manage_Variations,
		AUTH_Export_Orders,
		AUTH_Delete_Orders,
		AUTH_Order_Messages,
	);

	/**
	 * @var array An array of permissions in addition to the vendor permissions, that vendor administrators should be able to use.
	 */
	private $vendorAdminPermissions = array(
		AUTH_Manage_Users,
		AUTH_Add_User,
		AUTH_Edit_Users,
		AUTH_Delete_Users,
		AUTH_Statistics_Products,
		AUTH_Statistics_Orders,
		AUTH_Manage_ExportTemplates,
		AUTH_Import_Order_Tracking_Numbers,
		AUTH_Import_Products,
	);

	public function HandleToDo($Do)
	{
		$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('users');

		switch(isc_strtolower($Do)) {
			case "copyuser":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_User)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers", GetLang('CopyUser') => "index.php?ToDo=copyUser");

					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->CopyUser();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			case "edituser2":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers");

					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->EditUserStep2();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			case "edituser":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers", GetLang('EditUser1') => "index.php?ToDo=editeUser");

					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->EditUserStep1();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			case "createuser2":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_User)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers");

					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->AddUserStep2();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			case "createuser":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_User)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers", GetLang('CreateUser') => "index.php?ToDo=createUser");

					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->AddUserStep1();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			case "updateuserstatus":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users)) {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->UpdateUserStatus();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			case "deleteusers":
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Delete_Users)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers");

					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					$this->DeleteUsers();
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}

				break;
			}
			default:
			{
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
					$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Users') => "index.php?ToDo=viewUsers");

					if(!isset($_REQUEST['ajax'])) {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
					}

					$this->ManageUsers();

					if(!isset($_REQUEST['ajax'])) {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					}
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}
			}
		}
	}

	private function ManageUsersGrid(&$numUsers)
	{
		// Show a list of news in a table
		$page = 0;
		$start = 0;
		$numUsers = 0;
		$numPages = 0;
		$GLOBALS['UserGrid'] = "";
		$GLOBALS['Nav'] = "";
		$max = 0;

		if(isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'desc') {
			$sortOrder = 'desc';
		}
		else {
			$sortOrder = "asc";
		}

		$sortLinks = array(
			"User" => "username",
			"Name" => "name",
			"Email" => "useremail",
			"Status" => "userstatus",
			"Vendor" => "vendorname"
		);

		if(isset($_GET['sortField']) && in_array($_GET['sortField'], $sortLinks)) {
			$sortField = $_GET['sortField'];
			SaveDefaultSortField("ManageUsers", $_REQUEST['sortField'], $sortOrder);
		}
		else {
			list($sortField, $sortOrder) = GetDefaultSortField("ManageUsers", "username", $sortOrder);
		}

		if(isset($_GET['page'])) {
			$page = (int)$_GET['page'];
		} else {
			$page = 1;
		}

		$sortURL = sprintf("&sortField=%s&sortOrder=%s", $sortField, $sortOrder);
		$GLOBALS['SortURL'] = $sortURL;

		// Limit the number of questions returned
		if($page == 1) {
			$start = 1;
		} else {
			$start = ($page * ISC_USERS_PER_PAGE) - (ISC_USERS_PER_PAGE-1);
		}

		$start = $start-1;

		// Get the results for the query
		$userResult = $this->_GetUserList($start, $sortField, $sortOrder, $numUsers);
		$numPages = ceil($numUsers / ISC_USERS_PER_PAGE);

		// Add the "(Page x of n)" label
		if($numUsers > ISC_USERS_PER_PAGE) {
			$GLOBALS['Nav'] = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);
			$GLOBALS['Nav'] .= BuildPagination($numUsers, ISC_USERS_PER_PAGE, $page, sprintf("index.php?ToDo=viewUsers%s", $sortURL));
		}
		else {
			$GLOBALS['Nav'] = "";
		}

		$GLOBALS['Nav'] = rtrim($GLOBALS['Nav'], ' |');
		$GLOBALS['SortField'] = $sortField;
		$GLOBALS['SortOrder'] = $sortOrder;

		$GLOBALS['HideVendorColumn'] = 'display: none';
		if(gzte11(ISC_HUGEPRINT) && !$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$GLOBALS['HideVendorColumn'] = '';
		}

		BuildAdminSortingLinks($sortLinks, "index.php?ToDo=viewUsers&amp;page=".$page, $sortField, $sortOrder);

			// Workout the maximum size of the array
		$max = $start + ISC_USERS_PER_PAGE;

		if($max > count($userResult)) {
			$max = count($userResult);
		}

		if($numUsers > 0) {
			// Display the news
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($userResult)) {
				$GLOBALS['UserId'] = (int) $row['pk_userid'];

				if($row['vendorname']) {
					$GLOBALS['Vendor'] = "<a href='index.php?ToDo=editVendor&amp;vendorId=".$row['uservendorid']."'>".isc_html_escape($row['vendorname'])."</a>";
				}
				else {
					$GLOBALS['Vendor'] = GetLang('NA');
				}

				if($row['pk_userid'] == 1 || $row['username'] == "admin") {
					$GLOBALS['CheckDisabled'] = "DISABLED";
				} else {
					$GLOBALS['CheckDisabled'] = "";
				}

				if($row['name'] == " ") {
					$GLOBALS['Name'] = GetLang('NA');
				} else {
					$GLOBALS['Name'] = isc_html_escape($row['name']);
				}

				$GLOBALS['Username'] = isc_html_escape($row['username']);

				if(!$row['useremail']) {
					$GLOBALS['Email'] = GetLang('NA');
				}
				else {
					$GLOBALS['Email'] = sprintf("<a href='mailto:%s'>%s</a>", urlencode($row['useremail']), isc_html_escape($row['useremail']));
				}

				switch($row['userstatus'])
				{
					case 0: // Inactive
					{
						if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users)) {
							$GLOBALS['Status'] = sprintf("<a title='%s' href='index.php?ToDo=updateUserStatus&amp;userId=%d&amp;status=1'><img border='0' src='images/cross.gif'></a>", GetLang('UserActiveTip'), $row['pk_userid']);
						} else {
							$GLOBALS['Status'] = "<img border='0' src='images/cross.gif'>";
						}

						break;
					}
					case 1: // Active
					{
						if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users) && !($row['pk_userid'] == 1) ) {
							$GLOBALS['Status'] = sprintf("<a title='%s' href='index.php?ToDo=updateUserStatus&amp;userId=%d&amp;status=0'><img border='0' src='images/tick.gif'></a>", GetLang('UserInactiveTip'), $row['pk_userid']);
						} else {
							$GLOBALS['Status'] = "<img border='0' src='images/tick.gif'></a>";
						}

						break;
					}
				}

				// Can this account be edited?
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users)) {
					$GLOBALS['EditUserLink'] = sprintf("<a title='%s' class='Action' href='index.php?ToDo=editUser&amp;userId=%d'>%s</a>", GetLang('EditUser'), $row['pk_userid'], GetLang('Edit'));
				}
				else {
					$GLOBALS['EditUserLink'] = sprintf("<a class='Action' disabled>%s</a>", GetLang('Edit'));
				}

				// Can this account be copied?
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_User)) {
					$GLOBALS['CopyUserLink'] = sprintf("<a title='%s' class='Action' href='index.php?ToDo=copyUser&amp;userId=%d'>%s</a>", GetLang('CopyUser'), $row['pk_userid'], GetLang('Copy'));
				}
				else {
					$GLOBALS['CopyUserLink'] = sprintf("<a class='Action' disabled>%s</a>", GetLang('Copy'));
				}


				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("user.manage.row");
				$GLOBALS['UserGrid'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
			}
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("user.manage.grid");
			return $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
		}
	}

	public function ManageUsers($MsgDesc = "", $MsgStatus = "", $PendingUser = false)
	{
		// Fetch any results, place them in the data grid
		$numUsers = 0;
		$GLOBALS['UserDataGrid'] = $this->ManageUsersGrid($numUsers, $PendingUser);

		// Was this an ajax based sort? Return the table now
		if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
			echo $GLOBALS['UserDataGrid'];
			return;
		}

		if($MsgDesc != "") {
			$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
		}

		// Do we need to disable the delete button?
		if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Delete_Users) || $numUsers == 0) {
			$GLOBALS['DisableDelete'] = "DISABLED";
		}

		$GLOBALS['UserIntro'] = GetLang('ManageUserIntro');

		if($numUsers == 0) {
			// There are no users in the database
			$GLOBALS['DisplayGrid'] = "none";
		}

		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("user.manage");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	public function _GetUserList($Start, $SortField, $SortOrder, &$NumResults)
	{
		$queryWhere = '';
		if(isset($_REQUEST['vendorId'])) {
			$queryWhere .= " AND uservendorid='".(int)$_REQUEST['vendorId']."'";
		}

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$queryWhere .= " AND uservendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
		}

		$query = "
			SELECT u.*, v.vendorname, CONCAT(userfirstname, ' ', userlastname) AS name
			FROM [|PREFIX|]users u
			LEFT JOIN [|PREFIX|]vendors v ON (u.uservendorid=v.vendorid)
		";

		$query .= "WHERE 1=1 ".$queryWhere;
		$query .= "ORDER BY ".$SortField." ".$SortOrder;
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$NumResults = $GLOBALS['ISC_CLASS_DB']->CountResult($result);

		// Add the limit
		$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit($Start, ISC_USERS_PER_PAGE);

		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		return $result;
	}

	private function DeleteUsers()
	{
		if(isset($_POST['users'])) {
			$userids = implode(",", array_map('intval', $_POST['users']));
			$vendorRestriction = '';
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorRestriction = " AND uservendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
			}
			$query = sprintf("delete from [|PREFIX|]users where pk_userid in (%s) and pk_userid!=1".$vendorRestriction, $userids);
			$GLOBALS['ISC_CLASS_DB']->Query($query);
			$err = $GLOBALS['ISC_CLASS_DB']->_Error;

			if($err != "") {
				$this->ManageUsers($err, MSG_ERROR);
			} else {

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($_POST['users']));

				$this->ManageUsers(GetLang('UsersDeletedSuccessfully'), MSG_SUCCESS);
			}
		}
		else {
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$this->ManageUsers();
			} else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
			}
		}
	}

	private function UpdateUserStatus()
	{
		// Update the status of a user with a simple query
		$userId = (int)$_GET['userId'];
		$status = (int)$_GET['status'];

		$updatedUser = array(
			"userstatus" => $status
		);
		$vendorRestriction = '';
		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$vendorRestriction = " AND uservendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
		}
		$GLOBALS['ISC_CLASS_DB']->UpdateQuery("users", $updatedUser, "pk_userid='".$GLOBALS['ISC_CLASS_DB']->Quote($userId)."'".$vendorRestriction);
		if ($GLOBALS['ISC_CLASS_DB']->_Error == "") {
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$query = sprintf("SELECT username FROM [|PREFIX|]users WHERE pk_userid='%d'", $userId);
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$userName = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($userId, $userName);

				$this->ManageUsers(GetLang('UserStatusSuccessfully'), MSG_SUCCESS);
			}
			else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('UserStatusSuccessfully'), MSG_SUCCESS);
			}
		}
		else {
			$err = '';
			if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$this->ManageUsers(sprintf(GetLang('ErrUserStatusNotChanged'), $err), MSG_ERROR);
			} else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrUserStatusNotChanged'), $err), MSG_ERROR);
			}
		}
	}

	private function AddUserStep1()
	{
		if($message = str_strip($_REQUEST, '#')) {
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError(GetLang(B('UmVhY2hlZFVzZXJMaW1pdA==')), $message, MSG_ERROR);
			exit;
		}

		$GLOBALS['FormAction'] = "createUser2";
		$GLOBALS['Title'] = GetLang('CreateUser');
		$GLOBALS['PassReq'] = "<span class='Required'>*</span>";
		$GLOBALS['Adding'] = 1;
		$GLOBALS['XMLPath'] = sprintf("%s/xml.php", $GLOBALS['ShopPath']);
		$GLOBALS['XMLToken'] = md5(uniqid(true));

		if(!gzte11(ISC_HUGEPRINT)) {
			$GLOBALS['HideVendorOptions'] = 'display: none';
		}
		else {
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorDetails = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				$GLOBALS['HideVendorSelect'] = 'display: none';
				$GLOBALS['Vendor'] = $vendorDetails['vendorname'];
				$GLOBALS['HideAdminoptions'] = 'display: none';
			}
			else {
				$GLOBALS['VendorList'] = $this->GetVendorList();
				$GLOBALS['HideVendorLabel'] = 'display: none';
			}
		}

		$allowedPermissions = $this->GetPermissionList();
		$permissions = array();
		foreach($allowedPermissions as $group) {
			foreach(array_keys($group['permissions']) as $permission) {
				$permissions[] = $permission;
			}
		}

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$role = 'vendoradmin';
		}
		else {
			$role = 'admin';
		}

		$GLOBALS['PermissionSelects'] = $this->GeneratePermissionRows(array(), $permissions);
		$GLOBALS['UserRoleOptions'] = $this->GetUserRoleOptions($role);

		$GLOBALS['UserRoleSelectedAdmin'] = 'selected="selected"';
		$GLOBALS['HidePermissionSelects'] = 'display: none';

		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("user.form");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	private function AddUserStep2()
	{
		if($message = str_strip($_REQUEST, '#')) {
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError(GetLang(B('UmVhY2hlZFVzZXJMaW1pdA==')), $message, MSG_ERROR);
			exit;
		}

		// Get the information from the form and add it to the database
		$arrData = array();
		$arrPerms = array();
		$err = "";

		$this->_GetUserData(0, $arrData);
		$arrPerms = $this->_GetPermissionData(0);

		// Make sure the selected username is available
		if($this->_UsernameIsAvailable($arrData['username'])) {
			// The username is available, commit the data
			if($this->_CommitUser(0, $arrData, $arrPerms, $err)) {
				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($arrData['username']);

				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
					$this->ManageUsers(GetLang('UserAddedSuccessfully'), MSG_SUCCESS);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('UserAddedSuccessfully'), MSG_SUCCESS);
				}
			}
			else {
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
					$this->ManageUsers(sprintf(GetLang('ErrUserNotAdded'), $err), MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrUserNotAdded'), $err), MSG_ERROR);
				}
			}
		}
		else {
			// The selected username is taken
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$this->ManageUsers(sprintf(GetLang('ErrUsernameTaken'), isc_html_escape($arrData['username'])), MSG_ERROR);
			} else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrUsernameTaken'), $err), MSG_ERROR);
			}
		}
	}

	private function _GenerateUserPass()
	{
		// Generate a random string which is used as a password during the installer
		$token = "";

		for($i = 0; $i < rand(8, 12); $i++) {
			if(rand(1, 2) == 1) {
				$token .= chr(rand(65, 90));
			} else {
				$token .= chr(rand(48, 57));
			}
		}

		return $token;
	}

	public function _GenerateUserToken()
	{
		// Generate a random string which is used to store user credentials in the session
		$token = "";

		for($i = 0; $i < rand(20, 40); $i++) {
			if(rand(1, 2) == 1) {
				$token .= chr(rand(65, 90));
			} else {
				$token .= chr(rand(48, 57));
			}
		}

		return $token;
	}

	public function _CommitUser($UserId, &$Data, &$Perms, &$Err)
	{
		// Commit the details for the user account to the database
		$queries = array();
		$query = "";
		$err = null;

		if($UserId == 0) {
			// ----- Build the query for the user table -----

			$newUser = array(
				"username" => $Data['username'],
				"userpass" => $Data['userpass'],
				"userfirstname" => $Data['userfirstname'],
				"userlastname" => $Data['userlastname'],
				"userstatus" => (int)$Data['userstatus'],
				"useremail" => $Data['useremail'],
				"token" => $this->_GenerateUserToken(),
				"usertoken" => $Data['usertoken'],
				"userapi" => $Data['userapi'],
				'userrole' => $Data['userrole'],
			);

			if(gzte11(ISC_HUGEPRINT)) {
				$newUser['uservendorid'] = $Data['uservendorid'];
			}

			$UserId = $GLOBALS['ISC_CLASS_DB']->InsertQuery("users", $newUser);

			// Now build the permissions queries
			foreach($Perms as $p) {
				// Skip any permissions we don't have access to adjust
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() > 0 && !in_array($p, $this->vendorOnlyPermissions) && !in_array($p, $this->vendorAdminPermissions)) {
					continue;
				}
				$newPermission = array(
					"permuserid" => $UserId,
					"permpermissionid" => $p
				);
				$GLOBALS['ISC_CLASS_DB']->InsertQuery("permissions", $newPermission);
			}
		}
		else {
			$this->_GetUserData($UserId, $existingUser);
			$updatedUser = array(
				"usertoken" => $Data['usertoken'],
				"userapi" => $Data['userapi'],
				"userfirstname" => $Data['userfirstname'],
				"userlastname" => $Data['userlastname'],
				"useremail" => $Data['useremail']
			);

			if (isset($Data['userstatus'])) {
				$updatedUser['userstatus'] = (int)$Data['userstatus'];
			}

			if(isset($Data['userrole'])) {
				$updatedUser['userrole'] = $Data['userrole'];
			}

			if(gzte11(ISC_HUGEPRINT) && $UserId > 1) {
				$updatedUser['uservendorid'] = $Data['uservendorid'];
			}

			// Update the existing news post details. Firstly check the userId.
			// If it's 1 we're updating the super admin account
			if($UserId >= 1) {
				$updatedUser['username'] = $Data['username'];
			}

			// Does the user want to change the password?
			if(isset($Data['userpass']) && $Data['userpass'] != "") {
				$updatedUser['userpass'] = $Data['userpass'];
			}

			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("users", $updatedUser, "pk_userid='".$GLOBALS['ISC_CLASS_DB']->Quote($UserId)."'");

			// Setup the permissions queries for non super admin users only
			if($UserId != 1 && $updatedUser['username'] != "admin") {
				$query = sprintf("delete from [|PREFIX|]permissions where permuserid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($UserId));
				$GLOBALS['ISC_CLASS_DB']->Query($query);

				// Now build the permissions queries
				foreach($Perms as $p) {
					// Skip any permissions we don't have access to adjust
					if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() > 0 && !in_array($p, $this->vendorOnlyPermissions) && !in_array($p, $this->vendorAdminPermissions)) {
						continue;
					}
					$newPermission = array(
						"permuserid" => $UserId,
						"permpermissionid" => $p
					);
					$GLOBALS['ISC_CLASS_DB']->InsertQuery("permissions", $newPermission);
				}
			}
		}

		$err = $GLOBALS['ISC_CLASS_DB']->_Error;
		$Err = $err;

		if(is_null($err) || $err == "") {
			return true;
		} else {
			return false;
		}
	}

	private function _UsernameIsAvailable($Username)
	{
		$query = sprintf("select pk_userid from [|PREFIX|]users where username='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($Username));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		if ($GLOBALS['ISC_CLASS_DB']->CountResult($result) == 0) {
			return true;
		} else {
			return false;
		}
	}

	private function _GetPermissionData($UserId)
	{
		$RefArray = array();
		// Get the permissions for this user
		if ($UserId == 0) {
			// Get the user permissions from the form
			if(isset($_POST['permissions']) && is_array($_POST['permissions'])) {
				foreach($_POST['permissions'] as $type) {
					foreach($type as $p) {
						$RefArray[] = $p;
					}
				}
				sort($RefArray, SORT_NUMERIC);
			}
		} else {
			$query = sprintf("select * from [|PREFIX|]permissions where permuserid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($UserId));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$RefArray[] = $row['permpermissionid'];
			}
		}

		return $RefArray;
	}

	private function _GetUserData($UserId, &$RefArray)
	{
		// Get the data for the user and return it
		if ($UserId == 0) {
			// Get the details for the user from the form
			if (isset($_POST['userId']) && is_numeric($_POST['userId'])) {
				$RefArray['pk_userid'] = $_POST['userId'];
			} else {
				$RefArray['pk_userid'] = 0;
			}

			if (isset($_POST['username'])) {
				$RefArray['username'] = $_POST['username'];
			}

			if(isset($_POST['userpass']) && $_POST['userpass'] != '') {
				$RefArray['userpass'] = md5($_POST['userpass']);
			}

			$RefArray['useremail'] = $_POST['useremail'];
			$RefArray['userfirstname'] = $_POST['userfirstname'];
			$RefArray['userlastname'] = $_POST['userlastname'];

			if (isset($_POST['userstatus'])) {
				$RefArray['userstatus'] = $_POST['userstatus'];
			}

			if(isset($_POST['userrole'])) {
				$RefArray['userrole'] = $_POST['userrole'];
			}

			if(isset($_POST['userapi'])) {
				$RefArray['userapi'] = 1;
			}
			else {
				$RefArray['userapi'] = 0;
			}

			$RefArray['usertoken'] = $_POST['xmltoken'];
			$RefArray['uservendorid'] = 0;
			if(gzte11(ISC_HUGEPRINT)) {
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$RefArray['uservendorid'] = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
				}
				else {
					$RefArray['uservendorid'] = (int)$_POST['uservendorid'];
				}
			}
		}
		else {
			// Get the details from the database
			$query = sprintf("select * from [|PREFIX|]users where pk_userid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($UserId));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$RefArray = $row;
			}
		}
	}

	private function EditUserStep1()
	{
		// Show the form to edit a news
		$userId = (int)$_GET['userId'];
		$arrData = array();

		if(UserExists($userId)) {
			$this->_GetUserData($userId, $arrData);
			$arrPerms = $this->_GetPermissionData($userId);

			// Does this user have permission to edit this user?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $arrData['uservendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewUsers');
			}

			$GLOBALS['Username'] = isc_html_escape($arrData['username']);
			$GLOBALS['UserEmail'] = isc_html_escape($arrData['useremail']);
			$GLOBALS['UserFirstName'] = isc_html_escape($arrData['userfirstname']);
			$GLOBALS['UserLastName'] = isc_html_escape($arrData['userlastname']);

			$GLOBALS['XMLPath'] = sprintf("%s/xml.php", $GLOBALS['ShopPath']);
			$GLOBALS['XMLToken'] = isc_html_escape($arrData['usertoken']);

			if(!gzte11(ISC_HUGEPRINT)) {
				$GLOBALS['HideVendorOptions'] = 'display: none';
			}
			else {
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$vendorDetails = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					$GLOBALS['HideVendorSelect'] = 'display: none';
					$GLOBALS['Vendor'] = $vendorDetails['vendorname'];
					$GLOBALS['HideAdminoptions'] = 'display: none';
				}
				else {
					$GLOBALS['VendorList'] = $this->GetVendorList($arrData['uservendorid']);
					$GLOBALS['HideVendorLabel'] = 'display: none';
				}
			}

			if($arrData['userapi'] == "1") {
				$GLOBALS['IsXMLAPI'] = 'checked="checked"';
			}

			if($arrData['userstatus'] == 0) {
				$GLOBALS['Active0'] = 'selected="selected"';
			} else {
				$GLOBALS['Active1'] = 'selected="selected"';
			}

			if($arrData['userrole'] && $arrData['userrole'] != 'custom') {
				$GLOBALS['HidePermissionSelects'] = 'display: none';
			}

			// If the user is the super admin we need to disable some fields
			if($userId == 1 || $arrData['username'] == "admin") {
				$GLOBALS['DisableUser'] = "DISABLED";
				$GLOBALS['DisableStatus'] = "DISABLED";
				$GLOBALS['DisableUserType'] = "DISABLED";
				$GLOBALS['DisablePermissions'] = "DISABLED";
				$GLOBALS['HideVendorOptions'] = 'display: none';
			}

			$GLOBALS['PermissionSelects'] = $this->GeneratePermissionRows($arrData, $arrPerms);
			$GLOBALS['UserRoleOptions'] = $this->GetUserRoleOptions($arrData['userrole'], $arrData['uservendorid']);

			$GLOBALS['UserId'] = (int) $userId;
			$GLOBALS['FormAction'] = "editUser2";
			$GLOBALS['Title'] = GetLang('EditUser1');
			$GLOBALS['PassReq'] = "&nbsp;&nbsp;";

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("user.form");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}
		else {
			// The news post doesn't exist
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$this->ManageUsers(GetLang('UserDoesntExist'), MSG_ERROR);
			} else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
			}
		}
	}

	private function EditUserStep2()
	{
		// Get the information from the form and add it to the database
		$userId = $_POST['userId'];
		$arrData = array();
		$arrPerms = array();
		$err = "";
		$arrUserData = array();

		$this->_GetUserData($userId, $arrUserData);

		// Does this user have permission to edit this user?
		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $arrUserData['uservendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewUsers');
		}

		$this->_GetUserData(0, $arrData);
		if($arrUserData['pk_userid'] == 1 || $arrUserData['username'] == "admin") {
			$arrData['username'] = "admin";
		}

		$arrPerms = $this->_GetPermissionData(0);

		// Commit the values to the database
		if($this->_CommitUser($userId, $arrData, $arrPerms, $err)) {
			// Log this action
			if(!isset($arrData['username'])) {
				$arrData['username'] = 'admin';
			}

			$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($userId, $arrData['username']);

			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$this->ManageUsers(GetLang('UserUpdatedSuccessfully'), MSG_SUCCESS);
			} else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('UserUpdatedSuccessfully'), MSG_SUCCESS);
			}
		}
		else {
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Users)) {
				$this->ManageUsers(sprintf(GetLang('ErrUserNotUpdated'), $err), MSG_ERROR);
			} else {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrUserNotUpdated'), $err), MSG_ERROR);
			}
		}
	}

	private function GetUserRoleOptions($selectedRole='custom', $vendorId=0)
	{
		$userRoles = array();
		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() == 0) {
			$userRoles['sales'] = GetLang('UserRoleSales');
			$userRoles['manager'] = GetLang('UserRoleManager');
		}

		if(gzte11(ISC_HUGEPRINT)) {
			$userRoles['vendoradmin'] = GetLang('UserRoleVendorAdmin');
			$userRoles['vendor'] = GetLang('UserRoleVendor');
		}

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() == 0) {
			$userRoles['admin'] = GetLang('UserRoleAdmin');
			$userRoles['custom'] = GetLang('UserRoleCustom');
		}

		$roleList = '';
		foreach($userRoles as $role => $label) {
			$sel = '';
			if($selectedRole == $role) {
				$sel = 'selected="selected"';
			}
			$roleList .= '<option value="'.$role.'" '.$sel.'>'.isc_html_escape($label).'</option>';
		}
		return $roleList;

	}

	private function CopyUser()
	{
		if($message = str_strip($_REQUEST, '#')) {
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError(GetLang(B('UmVhY2hlZFVzZXJMaW1pdA==')), $message, MSG_ERROR);
			exit;
		}

		$userId = $_GET['userId'];
		$arrData = array();
		$arrPerms = array();

		$this->_GetUserData($userId, $arrData);

		// Does this user have permission to edit this user?
		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $arrData['uservendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewUsers');
		}

		$arrPerms = $this->_GetPermissionData($userId);

		$GLOBALS['Username'] = "";
		$GLOBALS['UserEmail'] = isc_html_escape($arrData['useremail']);
		$GLOBALS['UserFirstName'] = isc_html_escape($arrData['userfirstname']);
		$GLOBALS['UserLastName'] = isc_html_escape($arrData['userlastname']);

		if($arrData['userstatus'] == 0) {
			$GLOBALS['Active0'] = 'selected="selected"';
		} else {
			$GLOBALS['Active1'] = 'selected="selected"';
		}

		if($arrData['userrole'] && $arrData['userrole'] != 'custom') {
			$GLOBALS['HidePermissionSelects'] = 'display: none';
		}

		if(!gzte11(ISC_HUGEPRINT)) {
			$GLOBALS['HideVendorOptions'] = 'display: none';
		}
		else {
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorDetails = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				$GLOBALS['HideVendorSelect'] = 'display: none';
				$GLOBALS['Vendor'] = $vendorDetails['vendorname'];
			}
			else {
				$GLOBALS['VendorList'] = $this->GetVendorList($arrData['uservendorid']);
				$GLOBALS['HideVendorLabel'] = 'display: none';
			}
		}

		$GLOBALS['PermissionSelects'] = $this->GeneratePermissionRows($arrData, $arrPerms);
		$GLOBALS['UserRoleOptions'] = $this->GetUserRoleOptions($arrData['userrole'], $arrData['uservendorid']);

		$GLOBALS['FormAction'] = "createUser2";
		$GLOBALS['Title'] = GetLang('CopyUser');
		$GLOBALS['PassReq'] = "<span class='Required'>*</span>";
		$GLOBALS['Adding'] = 1;
		$GLOBALS['UserId'] = "";

		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("user.form");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	public function GetVendorList($selected=0)
	{
		$list = '';
		$query = "SELECT vendorname, vendorid FROM [|PREFIX|]vendors ORDER BY vendorname ASC";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		while($vendor = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			$sel = '';
			if($selected == $vendor['vendorid']) {
				$sel = 'selected="selected"';
			}
			$list .= '<option value="'.$vendor['vendorid'].'" '.$sel.'>'.isc_html_escape($vendor['vendorname']).'</option>';
		}
		return $list;
	}

	/**
	 * Return an available list of permissions that can be selected for a
	 * user. The permissions will be grouped by the user type/role.
	 *
	 * @return array An array of permissions, grouped by roles (admin, staff, manager etc)
	 */
	private function GetPermissionList()
	{
		static $permissions = array();

		// Save a few CPU cycles if we can
		if(!empty($permissions)) {
			return $permissions;
		}

		$permissions = array(
			'sales' => array(
				'title' => GetLang('SalesStaffPermissions'),
				'permissions' => array(
					AUTH_Manage_Orders => GetLang('ManageOrders'),
					AUTH_Edit_Orders => GetLang('EditOrders'),
					AUTH_Add_Orders => GetLang('AddOrders'),

					AUTH_Manage_Returns => GetLang('ManageReturns'),

					AUTH_Manage_Customers => GetLang('ManageCustomers'),
					AUTH_Edit_Customers => GetLang('EditCustomers'),

					AUTH_Manage_Reviews => GetLang('ManageReviews'),
					AUTH_Edit_Reviews => GetLang('EditReviews'),
					AUTH_Delete_Reviews => GetLang('DeleteReviews'),
					AUTH_Approve_Reviews => GetLang('ApproveReviews'),

					AUTH_Manage_Coupons => GetLang('ManageCoupons'),
					AUTH_Add_Coupons => GetLang('EditCoupons'),
					AUTH_Edit_Coupons => GetLang('AddCoupons'),
					AUTH_Delete_Coupons => GetLang('DeleteCoupons'),

					AUTH_Manage_Pages => GetLang('ManagePages'),
					AUTH_Add_Pages => GetLang('CreatePages'),
					AUTH_Edit_Pages => GetLang('EditPages'),
					AUTH_Delete_Pages => GetLang('DeletePages'),

					AUTH_Manage_Banners => GetLang('ManageBanners'),

					AUTH_Manage_GiftCertificates => GetLang('ManageGiftCertificates'),
				),
			),
			'manager' => array(
				'title' => GetLang('SalesManagerPermissions'),
				'permissions' => array(
					AUTH_Manage_Products => GetLang('ManageProducts'),
					AUTH_Create_Product => GetLang('CreateProducts'),
					AUTH_Edit_Products => GetLang('EditProducts'),
					AUTH_Delete_Products => GetLang('DeleteProducts'),
					AUTH_Export_Products => GetLang('ExportProducts1'),
					AUTH_Manage_Variations => GetLang('ProductVariations'),

					AUTH_Manage_Categories => GetLang('ManageCategories'),
					AUTH_Create_Category => GetLang('CreateCategories'),
					AUTH_Edit_Categories => GetLang('EditCategories'),
					AUTH_Delete_Categories => GetLang('DeleteCategories'),

					AUTH_Export_Orders => GetLang('ExportOrders'),
					AUTH_Delete_Orders => GetLang('DeleteOrders'),
					AUTH_Order_Messages => GetLang('OrderMessages'),

					AUTH_Add_Customer => GetLang('AddCustomers'),
					AUTH_Delete_Customers => GetLang('DeleteCustomers'),
					AUTH_Export_Customers => GetLang('ExportCustomers'),

					AUTH_Manage_News => GetLang('ManageNews'),
					AUTH_Add_News => GetLang('AddNews'),
					AUTH_Edit_News => GetLang('EditNews'),
					AUTH_Approve_News => GetLang('ApproveNews'),
					AUTH_Delete_News => GetLang('DeleteNews'),

					AUTH_Export_Froogle => GetLang('CreateFroogleFeed'),
					AUTH_View_XMLSitemap => GetLang('GoogleSitemap'),

					AUTH_Manage_Brands => GetLang('ManageBrands'),
					AUTH_Add_Brands => GetLang('AddBrands'),
					AUTH_Edit_Brands => GetLang('EditBrands'),
					AUTH_Delete_Brands => GetLang('DeleteBrands'),

					AUTH_Newsletter_Subscribers => GetLang('ExportSubscribers'),

					AUTH_Statistics_Overview => GetLang('ViewStatistics').': '.GetLang('StoreOverview'),
					AUTH_Statistics_Products => GetLang('ViewStatistics').': '.GetLang('ProductStatistics'),
					AUTH_Statistics_Orders => GetLang('ViewStatistics').': '.GetLang('OrderStatistics'),
					AUTH_Statistics_Customers => GetLang('ViewStatistics').': '.GetLang('CustomerStatistics'),
					AUTH_Statistics_Search => GetLang('ViewStatistics').': '.GetLang('SearchStatistics'),

					AUTH_Manage_ExportTemplates => GetLang('ManageExportTemplates'),

					AUTH_Website_Optimizer => GetLang('GoogleWebsiteOptimizer'),
				),
			),
			'admin' => array(
				'title' => GetLang('SystemAdministratorPermissions'),
				'permissions' => array(
					AUTH_Manage_Users => GetLang('ManageUsers'),
					AUTH_Add_User => GetLang('AddUsers'),
					AUTH_Edit_Users => GetLang('EditUsers'),
					AUTH_Delete_Users => GetLang('DeleteUsers'),

					AUTH_Manage_Vendors => GetLang('ManageVendors'),
					AUTH_Add_Vendors => GetLang('AddVendors'),
					AUTH_Edit_Vendors => GetLang('EditVendors'),
					AUTH_Delete_Vendors => GetLang('DeleteVendors'),

					AUTH_Manage_FormFields => GetLang('ManageFormFields'),
					AUTH_Add_FormFields => GetLang('AddFormFields'),
					AUTH_Edit_FormFields => GetLang('EditFormFields'),
					AUTH_Delete_FormFields => GetLang('DeleteFormFields'),

					AUTH_Manage_Settings => GetLang('ManageSettings'),

					AUTH_Import_Products => GetLang('ImportProducts'),
					AUTH_Import_Customers => GetLang('ImportCustomers'),

					AUTH_Import_Order_Tracking_Numbers => GetLang('ImportOrdertrackingnumbers'),

					AUTH_Manage_Backups => GetLang('ViewBackups'),

					AUTH_Manage_Templates => GetLang('ManageTemplates'),
					AUTH_Design_Mode => GetLang('DesignMode'),
					AUTH_Manage_Logs => GetLang('StoreLogs'),
					AUTH_Manage_Addons => GetLang('Addons'),

					AUTH_Customer_Groups => GetLang('CustomerGroups'),
					AUTH_System_Info => GetLang('SystemInfo'),
				),
			),
		);

		if(GetConfig('DisableAddons')) {
			unset($permissions['admin']['permissions'][AUTH_Manage_Addons]);
		}

		if(GetConfig('DisableSystemInfo')) {
			unset($permissions['admin']['permissions'][AUTH_System_Info]);
		}

		if(GetConfig('DisableBackupSettings')) {
			unset($permissions['admin']['permissions'][AUTH_Manage_Backups]);

		}

		// If this user is a vendor, they're only allowed to adjust certain permissions
		// so strip out everything else
		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			foreach($permissions as &$group) {
				foreach(array_keys($group['permissions']) as $permission) {
					if(!in_array($permission, $this->vendorOnlyPermissions) && !in_array($permission, $this->vendorAdminPermissions)) {
						unset($group['permissions'][$permission]);
					}
				}
			}
		}
		return $permissions;
	}

	/**
	 * Return a list of <option> tags for a particular type of user permissions.
	 * Values are also selected from the supplied array of permissions, if passed.
	 *
	 * @param string The type of permissions to generate the options for.
	 * @param array Optionally, the permissions that should be selected.
	 * @return string The generated HTML option tags.
	 */
	private function GetPermissionOptions($type, $selectedPermissions=array())
	{
		if(!is_array($selectedPermissions)) {
			$selectedPermissions = array();
		}

		$permissions = $this->GetPermissionList();
		if(!isset($permissions[$type]) || empty($permissions[$type]['permissions'])) {
			return '';
		}

		$options = '';
		foreach($permissions[$type]['permissions'] as $permission => $label) {
			$selected = '';
			if(in_array($permission, $selectedPermissions)) {
				$selected = 'selected="selected"';
			}
			$class = '';
			if(in_array($permission, $this->vendorOnlyPermissions)) {
				$class = 'vendoradmin_role vendor_role';
			}
			else if(in_array($permission, $this->vendorAdminPermissions)) {
				$class = 'vendoradmin_role';
			}
			$options .= '<option value="'.(int)$permission.'" class="'.$class.'" '.$selected.'>'.isc_html_escape($label).'</option>';
		}
		return $options;
	}

	/**
	 * Generate the available permission selection rows for the add/edit user form.
	 *
	 * @param array Array of details about the user were generating the list of permissions for.
	 * @param array Array of permissions the user currently has.
	 */
	private function GeneratePermissionRows($user=array(), $permissions=array())
	{
		$permissionRows = '';

		$supportedPermissions = $this->GetPermissionList();
		foreach($supportedPermissions as $code => $group) {
			$GLOBALS['GroupPermissions'] = $this->GetPermissionOptions($code, $permissions);
			if(!$GLOBALS['GroupPermissions']) {
				continue;
			}

			$GLOBALS['GroupTitle'] = isc_html_escape($group['title']);
			$GLOBALS['GroupCode'] = $code;
			$permissionRows .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('UserPermissionRow');
		}
		return $permissionRows;
	}
}
