<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11">
<html>
<head>
	<title>Seja benvido(a) a Instalação da Loja SPB 2012</title>
	<meta http-equiv="Content-Type" content="text/html; charset=%%GLOBAL_CharacterSet%%" />
	<meta name="robots" content="noindex, nofollow" />
	<style type="text/css">
		@import url("Styles/stylesinstall.css");
		@import url('Styles/tabmenu.css');
		@import url("Styles/iselector.css");
	</style>
	<!--[if IE]>
	<style type="text/css">
		@import url("Styles/ie.css");
	</style>
	<![endif]-->
	<style>
		h3 { font-size:13px; }
	</style>
	<script type="text/javascript">
		var url = 'remote.php';
		var critical_errors = "%%GLOBAL_CriticalErrors%%";
	</script>
	<script type="text/javascript" src="../javascript/jquery.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="script/menudrop.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/thickbox.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="script/common.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="script/install.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/iselector.js?%%GLOBAL_JSCacheToken%%"></script>
	<link rel="stylesheet" href="Styles/thickbox.css?%%GLOBAL_JSCacheToken%%" type="text/css" media="screen" />
</head>

<body>

	<form action="index.php?ToDo=RunInstallation" method="post" name="frmInstall" id="frmInstall">
	<div id="box">
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
		<table><tr><td style="border:solid 1px #85D342; padding:20px; background-color:#FFF; width:450px">
		<table>
		  <tr>
		    <td class="HelpInfo">
		      %%GLOBAL_Message%%
		      <div style="%%GLOBAL_HideInstallWarning%%" class="MessageBox MessageBoxInfo">
		        %%GLOBAL_InstallWarning%%
		        </div>
		      </td>
		    </tr>
		  <tr class="FormContent">
			<td>
				<table>

					<tr>
						<td nowrap style="padding:10px 10px 10px 0px" colspan="2"><h3>%%LNG_StoreDetails%%</h3></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_ShopPath%%:</td>
						<td><input type="text" name="ShopPath" id="ShopPath" class="Field250" value="%%GLOBAL_ShopPath%%"> <img onmouseout="HideHelp('shoppathhelp');" onmouseover="ShowHelp('shoppathhelp', '%%LNG_ShopPath%%', '%%LNG_ShopPathHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="shoppathhelp"></div></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_StoreCountryLocation%%:</td>
						<td>
							<select name="StoreCountryLocationId" id="StoreCountryLocationId" class="Field250">
								%%GLOBAL_StoreCountryList%%
							</select><img onmouseout="HideHelp('storecountrylocationhelp');" onmouseover="ShowHelp('storecountrylocationhelp', '%%LNG_StoreCountryLocation%%', '%%LNG_StoreCountryLocationHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="storecountrylocationhelp"></div>
						</td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_StoreCurrencyCode%%:</td>
						<td><input type="text" name="StoreCurrencyCode" id="StoreCurrencyCode" maxlength="3" class="Field50" value="BRL"> <img onmouseout="HideHelp('storecurrencycodehelp');" onmouseover="ShowHelp('storecurrencycodehelp', '%%LNG_StoreCurrencyCode%%', '%%LNG_StoreCurrencyCodeHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="storecurrencycodehelp"></div></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px">&nbsp;</td>
						<td>
							<label><input type="checkbox" name="installSampleData" id="installSampleData" value="1" %%GLOBAL_InstallSampleData%% /> %%LNG_InstallSampleData%%</label>
							<img onmouseout="HideHelp('sampledatahelp');" onmouseover="ShowHelp('sampledatahelp', '%%LNG_InstallSampleData%%', '%%LNG_InstallSampleDataHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="sampledatahelp"></div></td>
					</tr>
					<tr>
						<td nowrap style="padding:10px 10px 10px 0px" colspan="2"><h3>%%LNG_UserAccountDetails%%</h3></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_EmailAddress%%:</td>
						<td><input type="text" name="UserEmail" id="UserEmail" class="Field150" value="%%GLOBAL_UserEmail%%"> <img onmouseout="HideHelp('useremailhelp');" onmouseover="ShowHelp('useremailhelp', '%%LNG_EmailAddress%%', '%%LNG_InstallEmailAddressHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="useremailhelp"></div></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_ChooseAPassword%%:</td>
						<td><input type="password" name="UserPass" id="UserPass" class="Field150" value="%%GLOBAL_UserPass%%"> <img onmouseout="HideHelp('userpasshelp');" onmouseover="ShowHelp('userpasshelp', '%%LNG_ChooseAPassword%%', '%%LNG_ChooseAPasswordHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="userpasshelp"></div></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_ConfirmYourPassword%%:</td>
						<td><input type="password" name="UserPass1" id="UserPass1" class="Field150" value="%%GLOBAL_UserPass%%"> <img onmouseout="HideHelp('userpass1help');" onmouseover="ShowHelp('userpass1help', '%%LNG_ConfirmYourPassword%%', '%%LNG_ConfirmYourPasswordHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="userpass1help"></div></td>
					</tr>
					<tr>
						<td nowrap style="padding:10px 10px 10px 0px" colspan="2"><h3>%%LNG_MySQLDetails%%</h3></td>
					</tr>
					<tr>
						<td nowrap style="padding:0px 10px 0px 10px" colspan="2"><input type="radio" name="dbChoice" id="dbChoice1" value="ON"> <label for="dbChoice1">%%LNG_HasDB%%</label></td>
					</tr>
				</table>
				<table class="DBDetails" style="padding:10px 10px 10px 20px">
					<tr class="DBDetails">
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_DatabaseUser%%:</td>
						<td><input type="text" name="dbUser" id="dbUser" class="Field150" value="%%GLOBAL_dbUser%%"> <img onmouseout="HideHelp('dbuserhelp');" onmouseover="ShowHelp('dbuserhelp', '%%LNG_DatabaseUser%%', '%%LNG_DatabaseUserHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="dbuserhelp"></div></td>
					</tr>
					<tr class="DBDetails">
						<td nowrap style="padding:0px 10px 0px 10px">&nbsp;&nbsp; %%LNG_DatabasePassword%%:</td>
						<td><input type="password" name="dbPass" id="dbPass" class="Field150" value="%%GLOBAL_dbPass%%"> <img onmouseout="HideHelp('dbpasshelp');" onmouseover="ShowHelp('dbpasshelp', '%%LNG_DatabasePassword%%', '%%LNG_DatabasePasswordHelp%%')" src="images/help.gif" width="24" height="16" border="0">
						<div style="display:none" id="dbpasshelp"></div></td>
					</tr>
					<tr class="DBDetails">
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_DatabaseHostname%%:</td>
						<td><input type="text" name="dbServer" id="dbServer" class="Field150" value="%%GLOBAL_dbServer%%"> <img onmouseout="HideHelp('dbhostnamehelp');" onmouseover="ShowHelp('dbhostnamehelp', '%%LNG_DatabaseHostname%%', '%%LNG_DatabaseHostnameHelp%%')" src="images/help.gif" width="24" height="16" border="0"><div style="display:none" id="dbhostnamehelp"></div></td>
					</tr>
					<tr class="DBDetails">
						<td nowrap style="padding:0px 10px 0px 10px"><span class="Required">*</span> %%LNG_DatabaseName%%:</td>
						<td><input type="text" name="dbDatabase" id="dbDatabase" class="Field150" value="%%GLOBAL_dbDatabase%%"> <img onmouseout="HideHelp('dbnamehelp');" onmouseover="ShowHelp('dbnamehelp', '%%LNG_DatabaseName%%', '%%LNG_DatabaseNameHelp%%')" src="images/help.gif" width="24" height="16" border="0"><div style="display:none" id="dbnamehelp"></div></td>
					</tr>
					<tr class="DBDetails">
						<td nowrap style="padding:0px 10px 0px 10px">&nbsp;&nbsp; %%LNG_DatabaseTablePrefix%%:</td>
						<td><input type="text" name="tablePrefix" id="tablePrefix" class="Field150" value="%%GLOBAL_tablePrefix%%"> <img onmouseout="HideHelp('dbprefixhelp');" onmouseover="ShowHelp('dbprefixhelp', '%%LNG_DatabaseTablePrefix%%', '%%LNG_DatabaseTablePrefixHelp%%')" src="images/help.gif" width="24" height="16" border="0"><div style="display:none" id="dbprefixhelp"></div></td>
					</tr>
				</table>
				
				
	      <table>
					<tr>
					<td>&nbsp;</td>
						<td>
							<br /><input type="submit" name="SubmitButton" value="%%LNG_Continue%%" class="FormButton">
						</td>
					</tr>
					<tr>
						<td class="Gap"></td>
					</tr>
				</table>
			</td>
		  </tr>
		</table>
		</td></tr></table>
		<div style="padding:10px; margin-bottom:20px; text-align:center" class="PageFooter"><a href="http://www.scriptphpbr.com.br"><img src="images/logocriar.png" alt="Criação de Loja virtual SCRIPT PHP BR" /></a></div>
	</div>
	</form>

	<div id="permissionsBox" style="display:none">
		<div style="background-image:url('images/permissions_error.gif'); background-position:right bottom; background-repeat:no-repeat; height:100%">%%GLOBAL_PermissionErrors%%</div>
	</div>

	<script type="text/javascript">%%GLOBAL_AutoJS%%</script>

</body>
</html>