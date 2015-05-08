<script type="text/javascript">

// load language variables for the header image javascript
lang['HeaderImageConfirmDelete']   = "%%LNG_HeaderImageConfirmDelete%%";
lang['LayoutHeaderNoCurrentImage'] = "%%LNG_LayoutHeaderNoCurrentImage%%";
lang['LayoutHeaderImageNoImage']   = "%%LNG_LayoutHeaderImageNoImage%%";

var disableLoadingIndicator;
var CurrentVersion = '%%GLOBAL_TemplateVersion%%';

function ShowTab(T){
	i = 0;
	if('%%GLOBAL_HideMessageBox%%' == 'none'){
		$('#TemplateMsgBox').hide('normal');
	}

	while(document.getElementById("tab" + i) != null){
		document.getElementById("div" + i).style.display = "none";
		document.getElementById("tab" + i).className = "";
		i++;
	}

	document.getElementById("div" + T).style.display = "";
	document.getElementById("tab" + T).className = "active";
	document.getElementById("currentTab").value = T;
	SetCookie('templatesCurrentTab', T, 365);

	$(document).trigger('tabSelect' + T);
}

function launchDesignMode()
{
	window.open('%%GLOBAL_ShopPathNormal%%/?designModeToken=%%GLOBAL_DesignModeToken%%');
}

function get_random()
{
	var ranNum= Math.floor(Math.random()*105205);
	return ranNum;
}

function ChangeTemplateColor(link, preview, previewFull) {
	$(link).parents('div.TemplateBox').find('.previewImage').attr('src', preview);
	$(link).parents('div.TemplateBox').find('.previewImage').parents('a').attr('href', previewFull);
}

function DownloadTemplate(id, width, height) {
	tb_show('', 'index.php?ToDo=templateDownload&template='+id+'&height='+height+'&width='+width);
}


function LaunchEditor(){
	var win = window.open("designmode.php?ToDo=editFile&File=default.html&f=a");
	win.focus();
}

function CheckTemplateVersion(){
	// do the ajax request
	document.getElementById('TemplateVersionCheck').innerHTML = '<em>Checking Version...</em>';
	jQuery.ajax({ url: 'remote.php', type: 'POST', dataType: 'xml',
		data: {'w': 'checktemplateversion'},
		success: function(xml) {
			CheckTemplateVersionReturn(xml);
		}
	});
}

function CheckTemplateVersionReturn(xml){
	var  CurrentVersion = '%%GLOBAL_TemplateVersion%%';

	if($('status', xml).text() == 1){
		if($('version', xml).text() > CurrentVersion){
			document.getElementById('TemplateVersionCheck').innerHTML = '<img src="images/success.gif" align="absmiddle"> %%LNG_NewVersionAvailable%%'.replace('%%VERSION%%', $('version', xml).text());

			if ($.browser.msie){
				$('#TemplateVersionCheck').css("background-color","#99FF66");
			} else {
				$('#TemplateVersionCheck').show(0);
				$('#TemplateVersionCheck').css("background-color","#99FF66");
				$('#TemplateVersionCheck').animate({ backgroundColor: '#F9F9F9' }, { queue: true, duration: 1000 });
			}

			document.getElementById('TemplateVersionCheckButton').style.display = "none";
			document.getElementById('DownloadNewVersionButton').style.display = "";
		}else{
			document.getElementById('TemplateVersionCheck').innerHTML = '%%LNG_CurrentTemplateLatest%%';
		}
	}else {
		display_error('An Error has Occurred: ' + $('message', xml).text());
	}
}

function DownloadNewVersion(){
	if(confirm('Important Note: By downloading this new template you will completely override your current template files which will *not* be recoverable. If you have made any modifcations to your current template then you should backup your current template before continuing.\n\nTo download this template, click \'OK\'. To keep the current version, click the \'Cancel\' button.')){
		if($.browser.msie){
			tb_show('', "index.php?ToDo=templatedownload&template=%%GLOBAL_CurrentTemplateName%%&color=%%GLOBAL_CurrentTemplateColor%%&height=80&width=280&PreviewImage=%%GLOBAL_CurrentTemplateImage%%");
		}else{
			tb_show('', "index.php?ToDo=templatedownload&template=%%GLOBAL_CurrentTemplateName%%&color=%%GLOBAL_CurrentTemplateColor%%&height=58&width=240&PreviewImage=%%GLOBAL_CurrentTemplateImage%%");
		}
		document.getElementById('TemplateVersionCheckButton').style.display = "";
		document.getElementById('DownloadNewVersionButton').style.display = "none";
	}
}

function display_message(text,type){
	if(type=='error'){
		display_error('TemplateMsgBox', text);
	} else {
		display_success('TemplateMsgBox', text);
	}
}

lang.TemplateDownloadColorsConfirm = "%%LNG_JS_TemplateDownloadColorsConfirm%%";
$(window).resize(function() {
	// Remove the return statement to have the template list automatically
	// centered in the middle of the page. Apparently we don't want to do this at the moment.
	return;
	templateBoxWidth = $('.TemplateList .TemplateBox').width() + 20;
	$('.TemplateList').css({
		width: '100%'
	});
	width = $('.TemplateListContainer').width();
	numBoxes = Math.floor(width / templateBoxWidth);
	visibleBoxes = $('.TemplateBox:visible').length;
	if(visibleBoxes < numBoxes) {
		numBoxes = visibleBoxes;
	}
	left = (width - (numBoxes * templateBoxWidth)) / 2;
	$('.TemplateList').css({
		width: (templateBoxWidth * numBoxes) + 'px'
	});
});

$(document).ready(function() {
	$(window).trigger('resize');
	$('a.TplPreviewImage').fancybox({
		'zoomSpeedIn': 200,
		'zoomSpeedOut': 200,
		'overlayShow': false
	});
	
	$('.TemplateBox:not(.TemplateBoxOn)').hover(function() {
		$(this).addClass('TemplateBoxOver');
	}, function() {
		$(this).removeClass('TemplateBoxOver');
	});
	
	$('.TemplateBox a.ActivateLink').click(function() {
		templateBox = $(this).parents('.TemplateBox');
		templateId = templateBox.attr('class').match('TemplateId_([^ $]+)')[1];
		templateName = $('span.TemplateName', templateBox).html();
		templateColor = $('span.TemplateColor', templateBox).html();
		if(templateBox.hasClass('Installable')) {
			if($('.TemplateList .TemplateId_'+templateId).length > 1) {
				colorSchemes = '';
				$('.TemplateList .TemplateId_'+templateId).each(function() {
					templateColor = $('span.TemplateColor', this).html();
					colorSchemes += '- '+templateColor+"\n";
				});
				message = lang.TemplateDownloadColorsConfirm;
				message = message.replace(':templateName', templateName);
				message = message.replace(':templateColor', templateColor);
				message = message.replace(':colorList', colorSchemes);
				if(!confirm(message)) {
					return false;
				}
			}
			tb_show('', 'index.php?ToDo=templateDownload&template='+templateId+'&height=58&width=300&color='+templateColor);
		}
		else {
			window.location = 'index.php?ToDo=changeTemplate&template='+templateId+'&color='+templateColor;
		}
		return false;
	});
	
	$('.ShowTemplateTypes').change(function() {
		$('.NoTemplateMessage').hide();
		switch($(this).val()) {
			case 'installed':
				$('.TemplateBox').show();
				$('.TemplateBox.Installable').hide();
				break;
			case 'downloadable':
				$('.TemplateBox').hide();
				$('.TemplateBox.Installable').show();
				break;
			default:
				$('.TemplateBox').show();
		}
		$(window).trigger('resize');
		if($('.TemplateBox:visible').length == 0) {
			alert('%%LNG_JS_NoTemplatesAvailableFilter%%');
			$('.ShowTemplateTypes').val('all').trigger('change');
		}
	});
	
	// Scroll to the active template
	offsetTop = $('.TemplateBoxOn').offset().top;
	listTop = $('.TemplateList').offset().top;
	scrollTop = offsetTop - listTop - 20;
	if(scrollTop > 0) {
		$('.TemplateListContainer').scrollTop(scrollTop);
	}
});

</script>

<script type="text/javascript" src="../javascript/jquery/plugins/ajax.file.upload.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="../javascript/jquery/plugins/fancybox/fancybox.js?%%GLOBAL_JSCacheToken%%"></script>
<link rel="stylesheet" href="../javascript/jquery/plugins/fancybox/fancybox.css?%%GLOBAL_JSCacheToken%%" type="text/css" media="screen">

<script type="text/javascript" src="script/layout.headerimage.js?%%GLOBAL_JSCacheToken%%"></script>

	<div class="BodyContainer">
	<table class="OuterPanel">
		<tr>
			<td class="Heading1">%%LNG_ManageTemplates%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%GLOBAL_LayoutIntro%%</p>
			<p id="TemplateMsgBox">%%GLOBAL_Message%%</p>
		</td>
		</tr>
		<tr>
		<td class="Intro"><br />
			<form action="index.php" method="get">
			<input type="hidden" name="ToDo" value="viewTemplates">
		<ul id="tabnav">
				<li><a href="javascript:ShowTab(0)" class="active" id="tab0">%%LNG_TemplateSettings%%</a></li>
				<li><a href="javascript:ShowTab(1)" id="tab1">%%LNG_LogoSettings%%</a></li>
				<li><a href="javascript:ShowTab(3)" id="tab3">%%LNG_EmailTemplates%%</a></li>
				<li><a href="javascript:ShowTab(4)" id="tab4">%%LNG_FaviconImage%%</a></li>
				<li><a href="javascript:ShowTab(5)" id="tab5">%%LNG_HeaderImage%%</a></li>
		</ul>
			<input id="currentTab" name="currentTab" value="%%GLOBAL_ShowTab%%" type="hidden">
			</form>

		</td>
		</tr>

	</table>
	<div id="div0">
		<div class="Text">
			<div style="padding: 10px 0px 10px 10px">%%LNG_TemplateChoiceIntro%%</div>
		</div>

		<p class="MessageBox MessageBoxInfo" style="%%GLOBAL_HideSafeModeMessage%%; margin-top: 10px;">%%LNG_TemplateDownloadingSafeModeEnabled%%</p>

		<table class="Panel">
			<tr>
			  <td class="Heading2" colspan='2'>%%LNG_CurrentTemplate%%</td>
			</tr>
			<tr>
				<td align="left" width="200" style="padding:5px 5px 5px 10px;">
					<a href='%%GLOBAL_ShopPath%%/templates/%%GLOBAL_CurrentTemplateName%%/Previews/%%GLOBAL_CurrentTemplateImage%%' class="thickbox"><img src="thumb.php?tpl=%%GLOBAL_CurrentTemplateName%%&color=%%GLOBAL_CurrentTemplateImage%%" border="0" id="CurrentTemplateImage"></a>
				</td>
				<td align="left" valign="top"  style="padding:5px 5px 5px 10px;">
					<div class="TemplateHeading" id="CurrentTemplateHeading">%%GLOBAL_CurrentTemplateNameProper%% (%%GLOBAL_CurrentTemplateColor%%) - Version %%GLOBAL_TemplateVersion%%</div>
					<div id="TemplateFilesLocated">%%LNG_TemplateFilesLocated%%%%GLOBAL_CurrentTemplateName%%</div><br />

					<input type="button" value="%%LNG_BrowseTemplateFiles%%" class="SmallButton" class="Button" onclick="LaunchEditor();">	<input type="Button" class="SmallButton" onclick="CheckTemplateVersion();" value="%%LNG_CheckNewVersion%%"  id="TemplateVersionCheckButton"> <input type="Button" class="SmallButton" onclick="DownloadNewVersion();" value="%%LNG_DownloadNewVersion%%"  id="DownloadNewVersionButton" style="display:none; font-weight: bold;"><br /><br />
					<div id="TemplateVersionCheck"></div>
				</td>
			</tr>
	 </table><br />

	<table class="Panel" style="margin:0px;">
		<tr>
		  <td class="Heading2" colspan='2'>
			<span class="FloatRight">
				<strong>%%LNG_Filter%%</strong>
					<select name="templateType" class="ShowTemplateTypes">
					<option value="all">%%LNG_ShowAllTemplates%%</option>
					<option value="installed">%%LNG_ShowInstalledTemplates%%</option>
					<option value="downloadable">%%LNG_ShowNewTemplates%%</option>
				</select>
			</span>
			%%LNG_ChooseTemplate%%
		  </td>
		</tr>
		<tr>
			<td>
				<div class="TemplateListContainer">
					<div class="TemplateList">
						%%GLOBAL_TemplateListMap%%
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>

		<div id="div1" style="display:none">
		<!-- Start Logo Editor Tab -->
			%%GLOBAL_LogoTab%%
		<!-- End Logo Editor Tab -->
		</div>
		<div id="div2" style="display:none">
			<div class="Text" style="padding: 10px 0px 10px 10px">
				%%LNG_DesignModeIntro%%
				<ul>
					<li>%%LNG_DesignModeIntro2%%</li>
					<li>%%LNG_DesignModeIntro3%%</li>
					<li>%%LNG_DesignModeIntro4%%</li>
					<!--<li><a href="#" class="thickbox">%%LNG_DesignModeIntro5%%</a></li>-->
				</ul>

				<p>
					<input type="button" onclick="launchDesignMode();" value="%%LNG_LaunchDesignMode%%" />
				</p>
			</div>
		</div>
		<div id="div3" style="display:none">
			<div class="Text" style="padding: 10px 0px 10px 10px">
				%%LNG_EmailTemplatesIntro%%<br /><br />
				<table class="GridPanel SortableGrid AutoExpand" cellspacing="0" cellpadding="0" border="0" id="IndexGrid" style="width:100%;">
					<tr class="Heading3">
						<td>%%LNG_ETFileName%%</td>
						<td>%%LNG_ETFileSize%%</td>
						<td>%%LNG_ETLastUpdated%%</td>
						<td>%%LNG_Action%%</td>
					</tr>
					%%GLOBAL_EmailTemplatesGrid%%
				</table>
			</div>
		</div>

		<div id="div4" style="display: none;">
			<div class="Text" style="padding: 10px 0px 10px 10px">
				%%LNG_FaviconIntro%%
			</div>
			<form method="post" action="index.php?ToDo=TemplateUploadFavicon" enctype="multipart/form-data" onsubmit="return CheckFaviconForm();">
				<table class="Panel" style="margin:0px;">
					<tr>
						<td class="Heading2" colspan='2'>%%LNG_FaviconUpload%%</td>
					</tr>
					<tr>
						<td class="FieldLabel PanelBottom">
							%%LNG_SelectLogoUpload%%:
						</td>
						<td class="PanelBottom">
							<img src="%%GLOBAL_Favicon%%" width="16" height="16" />&nbsp;&nbsp;<input type="file" name="FaviconFile" id="FaviconFile" class="Field" value="" /> <input type="submit" value="%%LNG_UploadFavicon%%" />
						</td>
					</tr>
				</table>
			</form>
		</div>

		<div id="div5" style="display: none; ">
				<div class="Text" style="padding: 10px 0px 10px 10px;">%%LNG_LayoutHeaderImageIntro%%</div>

				<table class="Panel" style="margin:0px;">
					<tr>
					  <td class="Heading2" colspan='2'>%%LNG_LayoutHeaderImageGroupName%%</td>
					</tr>
					<tr>
						<td align="left" width="200" style="padding:5px 5px 5px 10px;" valign="top">
		%%LNG_LayoutHeaderImageCurrentImage%%:
						</td>
						<td align="left" valign="top"  style="padding:5px 5px 5px 10px;">
							<div id='currentHeaderImage'></div>
							<div id="DownloadHeaderImages" style="padding-top: 5px;">
							%%LNG_LayoutHeaderDownloadIntro%% <span id="BrowserBasedHelpText"></span>
							<ul>
								<li id="HeaderImageCurrentLinkContainer"><a href="#" id="">%%LNG_LayoutHeaderImageDownloadCurrentBG%%</a> (<a href="#" id="HeaderImageDeleteLink">%%LNG_LayoutHeaderImageDelete%%</a>)</li>
								<li><a href="#" id="HeaderImageOrigLink">%%LNG_LayoutHeaderImageDownloadWithoutBG%%</a></li>
								<li id="HeaderImageBlankLinkContainer"><a href="#" id="HeaderImageBlankLink">%%LNG_LayoutHeaderImageDownloadWithBG%%</a></li>
							</ul>
							</div>
						</td>
					</tr>

					<tr id="UploadHeaderImageRow" style="display:">
						<td align="left" width="200" valign="top"  style="padding:5px 5px 5px 10px;">
							%%LNG_LayoutHeaderImageUploadImage%%:
						</td>
						<td align="left" valign="top"  style="padding:5px 5px 5px 10px;" id="">

							<input type="file" name="HeaderImageFile" id="HeaderImageFile" class="Field300" value=""><br />
							<br /><input type="button" name="SubmitHeaderImageForm" id="SubmitHeaderImageForm" class="Button" value="%%LNG_LayoutHeaderImageUploadButton%%" />

						</td>
					</tr>
				</table>
		</div>

	</div>
	<div style="display: none" id="templateSelectedMessage"></div>

	<script type="text/javascript" defer>

		var DisplayTab = 0;
		var ForceTab = '%%GLOBAL_ForceTab%%';

		if(ForceTab.length > 0){
			DisplayTab = ForceTab;
		}

		DisplayTab = parseInt(DisplayTab);

		if(DisplayTab > -1){
			ShowTab(DisplayTab);
		}

		function edit_template(trID, tplfile) {
			$('#edit_'+trID).show();

			// Load the contents of the file
			jQuery.ajax({
				url: 'remote.php',
				type: 'POST',
				dataType: 'text',
				data: {'w': 'getEmailTemplate', 'file': tplfile, 'id': trID},
				success: function(txt) {
					$('#edit_box_'+trID).html(txt);
					if(typeof(tinyMCE) != 'undefined') {
						eval('LoadEditor_wysiwyg_'+trID+'()');
					}
				}
			});
		}

		function edit_hide(trID) {
			if(confirm("%%LNG_ETHideEdit%%")) {
				$('#edit_'+trID).hide();
			}
		}

		function save_edit(trID, tplfile) {
			if(typeof(tinyMCE) != 'undefined') {
				var html = tinyMCE.get('wysiwyg_'+trID).getContent();
			}
			else {
				var html = $("#wysiwyg_"+trID).val();
			}

			// Save the contents of the file
			jQuery.ajax({
				url: 'remote.php',
				type: 'POST',
				dataType: 'text',
				data: {'w': 'updateEmailTemplate', 'file': tplfile, 'html': html},
				success: function(status) {
					if(status == "success") {
						msg = "%%LNG_EmailTemplateUpdated%%";
					}
					else {
						msg = "%%LNG_EmailTemplateUpdateFailed%%";
					}
					alert(msg);
					$('#edit_'+trID).hide();
				}
			});
		}

		var EmailTemplates = {
			ExpandDirectory: function(row, directory)
			{
				$('#'+row+' .ExpandImg').blur();
				// Already expanded
				if($('#'+row).is('.Expanded')) {
					$('#'+row+' .ExpandImg').attr('src', $('#'+row+' .ExpandImg').attr('src').replace('minus.gif', 'plus.gif'));
					$('.Child_'+row).hide();
					$('#'+row).removeClass('Expanded');
					$('#Indicator_'+row).hide();
					return;
				}

				// We already have results, so just expand
				if($('.Child_'+row).length > 0) {
					$('#'+row+' .ExpandImg').attr('src', $('#'+row+' .ExpandImg').attr('src').replace('plus.gif', 'minus.gif'));
					$('.Child_'+row).show();
					$('#'+row).addClass('Expanded');
					return;
				}
				$('#Indicator_'+row).show();
				$.ajax({
					url: 'remote.php',
					data: {
						w: 'GetEmailTemplateDirectory',
						path: directory,
						parent: row
					},
					success: function(response) {
						$('#'+row+' .ExpandImg').attr('src', $('#'+row+' .ExpandImg').attr('src').replace('plus.gif', 'minus.gif'));
						if(response) {
							$('#Indicator_'+row).hide();
							$('#'+row).after(response);
						}
						else {
							$('#Indicator_'+row+' td').html('<span style="padding-left: 25px;"> %%LNG_DirectoryContainsNoFiles%%</span>');
						}
						$('#'+row).addClass('Expanded');
						$('.Child_'+row).hover(function() {
							$(this).addClass('GridRowOver');
						}, function() {
							$(this).removeClass('GridRowOver');
						});
					}
				})
			}
		}

		function CheckFaviconForm()
		{
			if (document.getElementById('FaviconFile').value == '') {
				alert('%%LNG_FaviconNoImageSelected%%');
				return false;
			}

			return true;
		}
	</script>
	<div style="display: none;">
		%%GLOBAL_TemporaryEditor%%
	</div>