<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>%%GLOBAL_ControlPanelTitle%%</title>
	<meta http-equiv="Content-Type" content="text/html; charset=%%GLOBAL_CharacterSet%%" />
	<meta name="robots" content="noindex, nofollow" />
	<style type="text/css">
		@import url("Styles/styles.css?%%GLOBAL_JSCacheToken%%");
		@import url('Styles/tabmenu.css?%%GLOBAL_JSCacheToken%%');
		@import url("Styles/iselector.css?%%GLOBAL_JSCacheToken%%");
		@import url('../javascript/jquery/plugins/imodal/imodal.css?%%GLOBAL_JSCacheToken%%');
		@import url('Styles/iconsearchbox.css?%%GLOBAL_JSCacheToken%%');
		%%GLOBAL_AdditionalStylesheets%%
	</style>
	<link rel="SHORTCUT ICON" href="%%GLOBAL_FaviconPath%%" />
	<!--[if IE]>
	<style type="text/css">
		@import url("Styles/ie.css?%%GLOBAL_JSCacheToken%%");
	</style>
	<![endif]-->

	<script type="text/javascript" src="../javascript/jquery.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="script/menudrop.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="script/common.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/iselector.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/thickbox.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/jquery/plugins/shiftcheckbox.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/jquery/plugins/ui.core.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/jquery/plugins/imodal/imodal.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/jquery/plugins/htmlEncode/jquery.htmlEncode.js?%%GLOBAL_JSCacheToken%%"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.GridPanel input:checkbox').shiftcheckbox();
		});
		config.ProductName = '%%GLOBAL_ProductName%%';
		var ThousandsToken = '%%GLOBAL_ThousandsToken%%';
		var DecimalToken = '%%GLOBAL_DecimalToken%%';
		var DimensionsThousandsToken = '%%GLOBAL_DimensionsThousandsToken%%';
		var DimensionsDecimalToken = '%%GLOBAL_DimensionsDecimalToken%%';
		%%GLOBAL_DefineLanguageVars%%
	</script>


	<link rel="stylesheet" href="Styles/thickbox.css?%%GLOBAL_JSCacheToken%%" type="text/css" media="screen" />
	<script type="text/javascript">
		var url = 'remote.php';
	</script>
	%%GLOBAL_RTLStyles%%
</head>

<body>
<div id="AjaxLoading"><img src="images/ajax-loader.gif" />&nbsp; %%LNG_LoadingPleaseWait%%</div>
%%GLOBAL_WarningNotices%%
<div class="Header">
	<div class="logo">
		<a href="index.php"><img src="images/logoadmin.png" alt="Criação de Loja virtual SCRIPT PHP BR" /></a>
	</div>

	<div class="textlinks">
		%%GLOBAL_textLinks%%
	</div>

	<div class="LoggedInAs">
		%%GLOBAL_CurrentlyLoggedInAs%%,
		<a href="index.php?ToDo=logOut" class="Logout">%%LNG_Logout%%?</a>
	</div>
</div>

<div class="menuBar">


	<div class="ControlPanelSearchBar">
		<form method="post" action="index.php?ToDo=quickSearch">
			<input id="quicksearch" onfocus="$(this).addClass('QuickSearchFocused'); if(!$(this).data('custom')) { $(this).val(''); }" onblur="if($(this).val()) { $(this).data('custom', 1); return; } $(this).removeClass('QuickSearchFocused'); if(!$(this).val()) { $(this).val('%%LNG_QuickSearchValue%%'); $(this).data('custom', 0); }" name="query" type="text" value="%%LNG_QuickSearchValue%%" />
		</form>
	</div>
	%%Panel.menubar%%
</div>

<div class="ContentContainer">

	<div id="PageBreadcrumb" class="Breadcrumb"  style="%%GLOBAL_HideBreadcrumb%%">
		<ul>
			%%GLOBAL_BreadcrumbTrail%%
		</ul>
	</div>

<script type="text/javascript">//<!--
	// not in DOM ready, we want this to happen ASAP so the user doesn't see a colour flicker
	$('#PageBreadcrumb ul li:last').addClass('Last').prev('li').addClass('SecondLast');
//--></script>

	%%GLOBAL_InfoTip%%
