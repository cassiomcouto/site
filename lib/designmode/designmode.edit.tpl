<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
<title>%%LNG_DesignMode%%</title>
<style type="text/css">
body {
	font-family: Arial;
	font-size: 12px;
	margin: 0;
	overflow: hidden;
	padding: 0;
}

.EditorHeader {
	background-color: #BFDBFF;
	color: #15428B;
	font-weight: bold;
	padding: 7px;
}

#Left {
	float: left;
	width: 250px;
}

#Sep {
	background: #BFDBFF;
	cursor: e-resize;
	float: left;
	height: 100%;
	width: 3px;
}

#FileEditor {
	float: left;
}

.ScrollableList {
	height: 200px;
	overflow: auto;
}

#EditorLoading {
	background: #fff;
	height: 100%;
	left: 0;
	position: absolute;
	top: 0;
	width: 100%;
	z-index: 200;
}

#FileEditor textarea {
	margin-left: 1px;
	padding: 4px;
}

#DesignModeMenu {
	background-image: url('../lib/designmode/images/DesignModeEditBg.gif');
	background-repeat: repeat-x;
	border-bottom: solid 1px #6F9DD9;
	font-family: Courier New;
	font-size: 9pt;
	width: 100%;
}

#DesignModeMenu div {
	float: left;
}

.DesignModeButton a {
	background: url('../lib/designmode/images/buttons.png') no-repeat;
	border: solid 1px transparent;
	color: #000;
	cursor: pointer;
	display: block;
	font-size: 11px;
	height: 22px;
	line-height: 22px;
	margin: 2px;
	padding: 0 4px 0 25px;
	text-decoration: none;
	font-family: Verdana;
	font-weight: normal;
}

.DesignModeSaveButton a {
	background-position: 3px -29px;
}

.DesignModeCloseButton a {
	background-position: 3px -125px;
}

.DesignModeCloseUpdateButton a {
	background-position: 3px -61px;
}

.DesignModeRecentFilesButton a {
	background-position: 3px -93px;
}

.DesignModeToggleButton a {
	background-position: 3px -157px;
}

.DesignModeRevertButton {
	position: absolute;
	top: 0;
	right: 0;
}

.DesignModeRevertButton a {
	background-position: 3px 3px;
}

.DesignModeButton a:hover {
	background-color: #FFE7A2;
	border: solid 1px #FFBD69;
}

.DesignModeRecentFilesButton a:hover {
	background-color: #fff;
	border-color: #5296F7;
}

.DesignModeRecentFilesButton a {
	position: relative;
	padding-right: 15px;
}

.DesignModeRecentFilesButton a span {
	width: 16px;
	height: 16px;
	background: url('../lib/designmode/images/buttons.png') no-repeat 3px -192px;
	display: block;
	position: absolute;
	right: 0;
	top: 6px;
}

.DesignModeButtonEnabled a {
	background-color: #FFE7A2;
	border: 1px solid #FFBD69;
}

.ScrollableList div.title {
	background: #FFF;
	font-weight: bold;
	padding: 5px;
	padding-left: 10px;
}

.ScrollableList ul, .ScrollableList li {
	list-style: none;
	margin: 0;
	padding: 0;
}

.ScrollableList a {
	border-bottom: 1px solid #fff;
	border-top: 1px solid #fff;
	color: #000;
	display: block;
	padding: 3px 0px 3px 20px;
	text-decoration: none;
}

.ScrollableList a:hover {
	background: #FFF0BE;
}

.ScrollableList .active a, .ScrollableList .active a:hover {
	background: #EFEFEF;
	font-weight: bold;
}

.ScrollableList .ActiveStylesheet {
	background: #E3EFFF;
}

#NoUsedFiles {
	background: #efefef;
	color: #bbb;
	font-size: 18px;
	font-weight: bold;
	line-height: 1.5;
	text-align: center;
}

#RecentFilesButton {
	position: relative;
	z-index: 9999;
}

.RecentFilesButtonOpen a,.RecentFilesButtonOpen a:hover {
	outline: none;
	background-color: #FFF;
	border: 1px solid #5296f7;
	border-bottom: 1px solid #fff;
	cursor: pointer;
	position: absolute;
	z-index: 20;
}

#RecentFiles, #RecentFiles li {
	list-style: none;
	margin: 0;
	padding: 0;
	z-index: 1;
}

#RecentFiles {
	margin-left: 2px;
	margin-top: -2px;
	background: #fff;
	border: 1px solid #5296f7;
	padding-top: 3px;
}

#RecentFiles li a {
	border: 1px solid #fff;
	color: #000;
	display: block;
	font-family: Verdana;
	font-size: 11px;
	margin: 0 3px;
	margin-bottom: 3px;
	padding: 3px;
	text-decoration: none;
}

#RecentFiles li a:hover {
	background: #c1d2ee;
	border: 1px solid #316ac5;
}

#EditorStatus {
	background: #ECFEE0;
	border: 1px solid green;
	color: green;
	font-weight: bold;
	margin: 1px;
	padding: 4px;
}

#EditorLoading {
	background: #fff url('../lib/designmode/images/LoadingBig.gif') no-repeat center;
	z-index: 1000000;
}

#EditorInlineLoading {
	background: #fff url('../lib/designmode/images/LoadingBig.gif') no-repeat center;
	border: 1px solid #000;
}

#FileEditorHeader {
	position: relative;
}

#RevertBackup, #RevertBackupOver {
	border: 1px solid transparent;
	position: absolute;
	right: 0;
	top: -1px;
}

#RevertBackupOver {
	border: solid 1px #316AC5;
}
</style>
<script type="text/javascript">
// Need to strip off in to design mode common file?
var Event = {
	observe: function(element, name, observer, useCapture)
	{
		if(!useCapture) useCapture = false;
		if(element.addEventListener)
			element.addEventListener(name, observer, useCapture);
		else
			element.attachEvent('on'+name, observer);
	},

	stopObserving: function(element, name, observer, useCapture)
	{
		if(!useCapture) useCapture = false;
		if(element.removeEventListener)
		{
			element.removeEventListener(name, observer, useCapture);
		}
		else
			element.detachEvent('on'+name, observer);
	},

	button: function(e)
	{

		if(!e && window.event) e = window.event;
		if(e.which) return e.which;
		else if(e.button) return e.button;
		else return false;
	},

	stop: function(e)
	{
		if(!e && window.event) e = window.event;
		if(e.preventDefault)
		{
		  e.preventDefault();
		  e.stopPropagation();
		}
		else
		{
		  e.returnValue = false;
		  e.cancelBubble = true;
		}
	},

	element: function(e)
	{
		if(e.target) return e.target;
		else if(e.srcElement) return e.srcElement;
		else return false;
	}
};

var Cookie = {
	get: function(name)
	{
		name = name += "=";
		var cookie_start = document.cookie.indexOf(name);
		if(cookie_start > -1) {
			cookie_start = cookie_start+name.length;
			cookie_end = document.cookie.indexOf(';', cookie_start);
			if(cookie_end == -1) {
				cookie_end = document.cookie.length;
			}
			return unescape(document.cookie.substring(cookie_start, cookie_end));
		}
	},

	set: function(name, value, expires)
	{
		if(!expires) {
			expires = "; expires=Wed, 1 Jan 2020 00:00:00 GMT;"
		} else {
			expire = new Date();
			expire.setTime(expire.getTime()+(expires*1000));
			expires = "; expires="+expire.toGMTString();
		}
		document.cookie = name+"="+escape(value)+expires;
	},

	unset: function(name)
	{
		Cookie.set(name, '', -1);
	}
};

function AjaxRequest(url, options)
{
	var request = null;
	var complete = false;
	this.url = url;

	if(!options)
		options = new Object();

	this.options = options;

	if(!this.options.method)
		this.options.method = "GET";
	if(!this.options.data)
		this.options.data = '';

	if(window.XMLHttpRequest)
		request = new XMLHttpRequest();
	else if(window.ActiveXObject)
		request = new ActiveXObject('Microsoft.XMLHTTP');

	if(!request)
		return false;


	request.open(this.options.method, url, true);
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	request.send(options.data);

	request.onreadystatechange = function() {
		if(request.readyState == 4)
		{
			complete = true;

			try
			{
				var content_type = request.getResponseHeader('Content-type');
				if(content_type && content_type.match(/^(text|application)\/(x-)?(java|ecma)script(;.*)?$/i))
				{
					eval(request.responseText);
				}

			}
			catch(e)
			{
			}

			// HTTP 200 OK
			if(request.status == 200)
			{
				if(options.onComplete)
				{
					options.onComplete(request);
				}
			}
			else if(options.onError)
			{
				options.onError(request);
			}
		}
	};
}

</script>

<script src="../lib/codepress/codepress.js" type="text/javascript"></script>
<script src="../lib/designmode/designmode_editor.js" type="text/javascript"></script>
<script src="../javascript/jquery.js" type="text/javascript"></script>
</head>
<body>
<div id="Editor">
	<div id="DesignModeMenu">
			<div class="DesignModeButton DesignModeSaveButton">
				<a href="javascript:DesignModeEditor.save();">%%LNG_Save%%</a>
			</div>
			<div class="DesignModeButton DesignModeCloseButton">
				<a href="javascript:DesignModeEditor.cancel();">%%LNG_Close%%</a>
			</div>
			<div class="DesignModeButton DesignModeCloseUpdateButton">
				<a href="javascript:DesignModeEditor.update();">%%LNG_CloseAndRefresh%%</a>
			</div>
			<div class="DesignModeButton DesignModeToggleButton">
				<a href="javascript:DesignModeEditor.toggle_editor();">%%LNG_ToggleEditor%%</a>
			</div>
			<div class="DesignModeButton DesignModeRecentFilesButton">
				<a href="#">%%LNG_RecentFiles%%<span></span></a>
			</div>
			<br style="clear: left;" />
	</div>

	<div id="Left">
		<div class="EditorHeader" id="FilesUsedHeader">%%LNG_FilesUsedByTemplate%%:</div>
		<div class="ScrollableList" id="FilesUsed">
			%%GLOBAL_PanelList%%

			%%GLOBAL_SnippetsList%%
		</div>

		<div class="EditorHeader" id="TemplateListHeader">%%LNG_OtherTemplateFiles%%</div>
		<div class="ScrollableList" id="TemplateList">
			<div class="title">%%LNG_Stylesheets%%</div>
			<ul>
				%%GLOBAL_StyleSheetFileList%%
			</ul>

			<div class="title">%%LNG_Layouts%%</div>
			<ul>
				%%GLOBAL_LayoutFileList%%
			</ul>

			<div class="title">%%LNG_Panels%%</div>
			<ul>
				%%GLOBAL_PanelFileList%%
			</ul>

			<div class="title">%%LNG_Snippets%%</div>
			<ul>
				%%GLOBAL_SnippetFileList%%
			</ul>

		</div>
	</div>

	<div id="Sep">&nbsp;</div>

	<div id="FileEditor">
		<div class="EditorHeader" id="FileEditorHeader">
			<div class="DesignModeButton DesignModeRevertButton">
				<a href="javascript:DesignModeEditor.revert_to_original()">%%LNG_RevertToOriginal%%</a>
			</div>
			<div>%%LNG_ContentsOfFile%% <span id="CurrentFile"></span></div>
		</div>
		<div id="EditorStatus" class="success" style="display: none;">&nbsp;</div>
		<textarea id="editorBox" name="editorBox" class="html autocomplete-off"></textarea>
	</div>
<br style="clear: both;" />
</div>

<div id="EditorLoading">
</div>

<div id="EditorInlineLoading" style="display: none;">
&nbsp;
</div>

<script type="text/javascript">
var Lang = {
	DesignModeConfirmCancel: "%%LNG_DesignModeConfirmCancel%%",
	DesignModeChangeFile: "%%LNG_DesignModeChangeFile%%",
	DesignModeLoadUnsaved: "You have unsaved changes in this file.\n\nLoad new file without saving changes?",
	ContainsNoSnippetsPanels: "This file does not contain any panels or snippets.",
	DesignModeRevert: "Revert this file to its original content?\n\nYou will lose any customisations or unsaved changes."
};

%%GLOBAL_LoadFileJS%%
%%GLOBAL_SavedOKAlert%%
</script>
</body>
</html>