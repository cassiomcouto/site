
	<form enctype="multipart/form-data" action="index.php?ToDo=%%GLOBAL_FormAction%%" onsubmit="return ValidateForm(CheckUserForm)" id="frmUser" method="post">
	<input type="hidden" name="userId" value="%%GLOBAL_UserId%%">
	<div class="BodyContainer">
	<table class="OuterPanel">
	  <tr>
		<td class="Heading1" id="tdHeading">%%GLOBAL_Title%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%LNG_UserIntro%%</p>
			%%GLOBAL_Message%%
			<p>
				<input type="submit" name="SaveButton1" value="%%LNG_Save%%" class="FormButton">&nbsp;
				<input type="button" name="CancelButton1" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()">
			</p>
		</td>
	  </tr>
		<tr>
			<td>
			  <table class="Panel">
				<tr>
				  <td class="Heading2" colspan=2>%%LNG_NewUserDetails%%</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_Username%%:
					</td>
					<td>
						<input type="text" id="username" name="username" class="Field250" autocomplete="off" value="%%GLOBAL_Username%%" %%GLOBAL_DisableUser%%>
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						%%GLOBAL_PassReq%%&nbsp;%%LNG_UserPass%%:
					</td>
					<td>
						<input type="password" id="userpass" name="userpass" class="Field250" autocomplete="off" value="%%GLOBAL_UserPass%%">
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						%%GLOBAL_PassReq%%&nbsp;%%LNG_UserPass1%%:
					</td>
					<td>
						<input type="password" id="userpass1" name="userpass1" class="Field250" autocomplete="off" value="%%GLOBAL_UserPass%%">
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;%%LNG_UserEmail%%:
					</td>
					<td>
						<input type="text" id="useremail" name="useremail" class="Field250" value="%%GLOBAL_UserEmail%%">
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_UserFirstName%%:
					</td>
					<td>
						<input type="text" id="userfirstname" name="userfirstname" class="Field250" value="%%GLOBAL_UserFirstName%%">
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_UserLastName%%:
					</td>
					<td>
						<input type="text" id="userlastname" name="userlastname" class="Field250" value="%%GLOBAL_UserLastName%%">
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_UserStatus%%:
					</td>
					<td>
						<select id="userstatus" name="userstatus" class="Field250" %%GLOBAL_DisableStatus%%>
							<option value="1" %%GLOBAL_Active1%%>%%LNG_UserActive%%</option>
							<option value="0" %%GLOBAL_Active0%%>%%LNG_UserInactive%%</option>
						</select>
						<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '%%LNG_UserStatus%%', '%%LNG_UserStatusHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="d1"></div><br />
					</td>
				</tr>
				<tr style="%%GLOBAL_HideVendorOptions%%">
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_Vendor%%:
					</td>
					<td>
						<div style="%%GLOBAL_HideVendorSelect%%">
							<select id="uservendorid" name="uservendorid" class="Field250">
								<option value="">%%LNG_UserNoVendor%%</option>
								%%GLOBAL_VendorList%%
							</select>
							<img onmouseout="HideHelp('uservendorhelp');" onmouseover="ShowHelp('uservendorhelp', '%%LNG_Vendor%%', '%%LNG_VendorHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="uservendorhelp"></div>
						</div>
						<div style="%%GLOBAL_HideVendorLabel%%">
							%%GLOBAL_Vendor%%
						</div>
					</td>
				</tr>
				<tr><td class="Gap"></td></tr>
				<tr><td class="Sep" colspan="2"></td></tr>
			 </table>
			</td>
		</tr>
		<tr>
			<td>
			  <table class="Panel">
				<tr>
				  <td class="Heading2" colspan=2>%%LNG_Permissions%%</td>
				</tr>
			</table>
			<table class="Panel">
				<tr>
					<td colspan="2">
						<p class="HelpInfo">
							%%LNG_PermissionsHelp1%% <a href="javascript:void(0)" onclick="LaunchHelp(686)">%%LNG_PermissionsHelp2%%</a>.
						</p>
					</td>
				</tr>
			</table>
			<table class="Panel">
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp;&nbsp;%%LNG_UserRole%%:
					</td>
					<td>
						<select name="userrole" class="Field250" onchange="UpdateRole(this.options[this.selectedIndex].value)" %%GLOBAL_DisablePermissions%%>
							%%GLOBAL_UserRoleOptions%%
						</select>
						<img onmouseout="HideHelp('userrolehelp');" onmouseover="ShowHelp('userrolehelp', '%%LNG_UserRole%%', '%%LNG_UserRoleHelp%%')" src="images/help.gif" alt="" />
						<div style="display:none" id="userrolehelp"></div>
					</td>
				</tr>
				%%GLOBAL_PermissionSelects%%
				<tr>
					<td class="FieldLabel">
						&nbsp;&nbsp; <label for="StoreName">%%LNG_EnableXMLAPI%%?</label>
					</td>
					<td>
						<input type="checkbox" name="userapi" id="userapi" value="ON" %%GLOBAL_IsXMLAPI%% /> <label for="userapi">%%LNG_YesEnableXMLAPI%%</label>
						<img onmouseout="HideHelp('xmlapi');" onmouseover="ShowHelp('xmlapi', '%%LNG_EnableXMLAPI%%', '%%LNG_EnableXMLAPIHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="xmlapi"></div><br />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="LaunchHelp(683)" style="color:gray">%%LNG_WhatIsXMLAPI%%</a><br/><br />
						<table cellspacing="0" cellpadding="2" border="0" class="panel" style="display: block;" id="sectionXMLToken" style="display:none">
							<tr>
								<td width="90">
									<img width="20" height="20" src="images/nodejoin.gif"/>&nbsp; %%LNG_XMLPath%%:
								</td>
								<td>
									<input type="text" readonly="" class="Field250" value="%%GLOBAL_XMLPath%%" id="xmlpath" name="xmlpath"/><img onmouseout="HideHelp('xmlpathhelp');" onmouseover="ShowHelp('xmlpathhelp', '%%LNG_XMLPath%%', '%%LNG_XMLPathHelp%%')" src="images/help.gif" width="24" height="16" border="0">
									<div style="display:none" id="xmlpathhelp"></div>
								</td>
							</tr>
							<tr>
								<td width="90">
									<img width="20" height="20" src="images/blank.gif"/>&nbsp; %%LNG_XMLToken%%:
								</td>
								<td>
									<input type="text" onfocus="select(this);" readonly="" class="Field250" value="%%GLOBAL_XMLToken%%" id="xmltoken" name="xmltoken"/> <img onmouseout="HideHelp('xmltokenhelp');" onmouseover="ShowHelp('xmltokenhelp', '%%LNG_XMLToken%%', '%%LNG_XMLTokenHelp%%')" src="images/help.gif" width="24" height="16" border="0">
									<div style="display:none" id="xmltokenhelp"></div>
								</td>
							</tr>
							<tr>
								<td>
									&nbsp;
								</td>
								<td>
									<a style="color: gray;" href="javascript:void(0)" id="regenlink">%%LNG_RegenerateToken%%</a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="Gap">&nbsp;</td>
					<td class="Gap">
						<input type="submit" name="SaveButton2" value="%%LNG_Save%%" class="FormButton">&nbsp;
						<input type="button" name="CancelButton2" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()">
					</td>
				</tr>
				<tr><td class="Gap"></td></tr>
				<tr><td class="Gap"></td></tr>
				<tr><td class="Sep" colspan="2"></td></tr>
			 </table>
			</td>
		</tr>

	</table>

	</div>
	</form>

	<script type="text/javascript">
		function UpdateRole(role)
		{
			// Start our selections
			if(role == 'admin') {
				SetupPermissions('sales', true);
				SetupPermissions('manager', true);
				SetupPermissions('admin', true);
			}
			else if(role == 'manager') {
				SetupPermissions('sales', true);
				SetupPermissions('manager', true);
				SetupPermissions('admin', false);
			}
			else if(role == 'sales') {
				SetupPermissions('sales', true);
				SetupPermissions('manager', false);
				SetupPermissions('admin', false);
			}
			else {
				// Revert all permissions
				SetupPermissions('sales', false);
				SetupPermissions('manager', false);
				SetupPermissions('admin', false);

				// Now reselect based on the role
				$('.permission_select .'+role+'_role input').attr('checked', false);
				$('.permission_select .'+role+'_role input').trigger('click');
			}
		}

		function ConfirmCancel()
		{
			if(confirm("%%LNG_ConfirmCancelUser%%"))
				document.location.href = "index.php?ToDo=viewUsers";
		}

		function CheckUserForm()
		{
			var un = document.getElementById("username");
			var up1 = document.getElementById("userpass");
			var up2 = document.getElementById("userpass1");
			var ue = document.getElementById("useremail");

			if(un.value == "")
			{
				alert("%%LNG_UserEnterUsername%%");
				un.focus();
				return false;
			}

			if("%%GLOBAL_Adding%%" == "1")
			{
				if(up1.value == "")
				{
					alert("%%LNG_UserEnterPassword%%");
					up1.focus();
					return false;
				}

				if(up1.value != up2.value)
				{
					alert("%%LNG_UserPasswordsDontMatch%%");
					up2.focus();
					up2.select();
					return false;
				}
			}
			else
			{
				if( (up1.value != "" || up2.value != "") && (up1.value != up2.value))
				{
					alert("%%LNG_UserPasswordsDontMatch%%");
					up2.focus();
					up2.select();
					return false;
				}
			}

			if(ue.value.indexOf(".") == -1 || ue.value.indexOf("@") == -1)
			{
				alert("%%LNG_UserInvalidEmail%%");
				ue.focus();
				ue.select();
				return false;
			}

			if(!HasSelectedPermissions('sales') && !HasSelectedPermissions('manager') && !HasSelectedPermissions('admin')) {
				$('#permissions_sales').focus();
				alert("%%LNG_UserNoPermissions%%");
				return false;
			}

			// Everything is OK
			return true;
		}

		function HasSelectedPermissions(type) {
			if(g('permissions_'+type+'_old')) {
				var f = $('#permissions_'+type+'_old').val();
			}
			else {
				var f = $('#permissions_'+type).val();
			}
			return f;
		}

		function SetupPermissions(type, status)
		{
			if($('#permissions_'+type).length != 1) {
				return;
			}

			if($('#permissions_'+type+'_old').length == 1) {
				if($('#permissions_'+type+'_old').attr('disabled') == true) {
					return;
				}

				$('#permissions_'+type+' input').attr('checked', !status);
				$('#permissions_'+type+' input').trigger('click');
			}
			else {
				$('#permissions_'+type+' option').attr('selected', status);
			}
		}

		function ToggleAPI(State) {
			if(State) {
				$('#sectionXMLToken').show();
			}
			else {
				$('#sectionXMLToken').hide();
			}
		}

		function RegenerateToken() {
			$.get("%%GLOBAL_ShopPath%%/admin/remote.php?w=generateAPIKey", null, function(data) { $('#xmltoken').val(data); } );
		}

		$(document).ready(function() {
			if('%%GLOBAL_IsXMLAPI%%' == 'checked="checked"') {
				ToggleAPI(true);
			}
			else {
				ToggleAPI(false);
			}
		});

		$('#userapi').click(function() {
			if($('#userapi').attr('checked')) {
				ToggleAPI(true);
				if($('#xmltoken').val() == '') {
					RegenerateToken();
				}
			}
			else {
				ToggleAPI(false);
			}
		});

		$('#regenlink').click(function() {
			RegenerateToken();
		});

	</script>
