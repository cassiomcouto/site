<style type="text/css">
.swfupload {
	position: absolute;
	z-index: 1;
	outline: none;
}
</style>
<!-- QmFkLVN5bnRheC1MZWV0V29sZg== -->
<script type="text/javascript" src="../javascript/jquery/plugins/jquery.htmlEncode.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="../javascript/jquery.growinguploader.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="../javascript/jquery/plugins/ajax.file.upload.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="script/detect.flash.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="script/swfupload.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="script/swfupload.handlers.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript" src="script/multiuploaddialog.js?%%GLOBAL_JSCacheToken%%"></script>
<script type="text/javascript">
		var swfUploadMaxFileSize = '%%GLOBAL_MaxFileSize%%';

		function randomString(length) {
			var chars = "0123456789abcdefghiklmnopqrstuvwxyz";
			var string_length = 8;
			var randomstring = '';
			for (var i=0; i<string_length; i++) {
				var rnum = Math.floor(Math.random() * chars.length);
				randomstring += chars.substring(rnum,rnum+1);
			}
			return randomstring;
		}

		var swfu;
		var MaxFileSize = '%%GLOBAL_MaxFileSize%%';
		var global_randNum = randomString(10);
		var requiredFlashMajorVersion = 8;
		var requiredFlashMinorVersion = 0;
		var requiredFlashRevision = 0;
		var TotalItemsToUpload = 0;
		var UploadErrorFiles = new Array();
		var UploadDuplicateFiles = new Array();
		var FileCount = 1;
		var hasReqestedFlashVersion = false;// DetectFlashVer(requiredFlashMajorVersion, requiredFlashMinorVersion, requiredFlashRevision);

		(function($) {
			 $.evalJSON = function(src)
			// Evals JSON that we know to be safe.
			{
				eval('var json = ' + src + ';');
				return json;
			};

		})(jQuery);

		$(document).ready(function() {
			jQuery.fn.exists = function() {
				return ( this.is('*') );
			}

			if (hasReqestedFlashVersion) {
				swfu = new SWFUpload({
					// Backend Settings
					upload_url: "%%GLOBAL_AppPath%%/admin/remote.php?remoteSection=imagemanager&w=uploadimage",	// Relative to the SWF file or absolute
					// File Upload Settings
					file_size_limit : "2 MB",	// 2MB
					file_types : "*.jpg;*.gif;*.png;*.tiff,*.bmp,*.jpeg",
					file_types_description : " Images",
					file_upload_limit : "0",

					post_params: {"PHPSESSID": "%%GLOBAL_sessionid%%"},

					// Event Handler Settings
					file_queue_error_handler : fileQueueError,
					file_dialog_complete_handler : fileDialogComplete,
					upload_progress_handler : uploadProgress,
					upload_error_handler : uploadError,
					upload_success_handler : uploadSuccess,
					upload_complete_handler : uploadComplete,

					// Button Settings
					button_placeholder_id : "spanButtonPlaceholder",
					button_width: 130,
					button_height: 22,
					button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
					button_cursor: SWFUpload.CURSOR.HAND,

					// Flash Settings
					flash_url : "images/swfupload.swf",

					custom_settings : {
						upload_target : "divFileProgressContainer"
					},

					// Debug Settings
					debug: false
				});
			} else { // no flash installed!
				$('#btnUpload').click(function () {
					var dialog = new MultiUploadDialog({
						action: 'remote.php?remoteSection=imagemanager&w=uploadimage&unique=' + global_randNum,
						titletext: '%%LNG_JS_UploadingImages%%',
						introtext: '%%LNG_JS_noFlashImageUploadIntro%%',
						submittext: '%%LNG_JS_Upload%%',
						closetext: '%%LNG_JS_Cancel%%',
						cleartext: '%%LNG_JS_Remove%%',
						noinputsalerttext: '%%LNG_JS_UploadImagesChooseAnImage%%'
					});

					$(dialog).bind('uploadsuccess', function(evt, result){
						if (result.Filedata.duplicate) {
							UploadDuplicateFiles.push(result.Filedata.name);
						} else if (result.Filedata.errorfile != '') {
							UploadErrorFiles.push(result.Filedata.name);
						} else if (result.Filedata.error == 0) {
							// success!
							AdminImageManager.AddImage(result.Filedata.name, '../product_images/uploaded_images/' + result.Filedata.name,  result.Filedata.filesize, result.Filedata.width, result.Filedata.height, result.Filedata.origwidth + ' x ' + result.Filedata.origheight,  result.Filedata.id);
						}
					});

					$(dialog).bind('uploadsfinished', function(evt){
						if(UploadErrorFiles.length > 0){
							var imageList = '';
							var thisImage = '';
							for(var i = 0; i < UploadErrorFiles.length; i++){
								thisImage = UploadErrorFiles[i];
								imageList += '<li>' + $('<p>' + thisImage + '</p>').text() + '</li>'; // strips out any html
							}
							if(UploadErrorFiles.length == TotalItemsToUpload){
								display_error('MainMessage', 'The following images were not uploaded because they are not valid image files: <ul>' + imageList + '</ul>');
							}else{
								display_error('MainMessage', 'The following images were not uploaded because they are not valid image files (Any image not listed here was uploaded successfully) <ul>' + imageList + '</ul>');
							}
						}else if(UploadDuplicateFiles.length > 0){
							var imageList = '';
							var thisImage = '';
							for(var i = 0; i < UploadDuplicateFiles.length; i++){
								thisImage = UploadDuplicateFiles[i];
								imageList += '<li>' + $('<p>' + thisImage + '</p>').text() + '</li>'; // strips out any html
							}
							display_error('MainMessage', 'All images were uploaded sucessfully with the exception of the following which were found to be duplicates. Please rename these files and try again. <ul>' + imageList + '</ul>');
						}else{
							// The 4 selected images have been uploaded and are shown below
							// The selected image has been uploaded and is shown below.
							if(FileCount == 1){
								display_success('MainMessage', 'The selected image has been uploaded and is shown below.');
							}else{
								display_success('MainMessage', 'The ' + FileCount + ' selected images have been uploaded and are shown below.');
							}
						}
					});

				});
			}

			$('.DeleteImageCheckbox').live('click', function(){
				if($('.DeleteImageCheckbox:checked').size() < 1) {
					$('#toggleAllChecks').removeAttr('checked');
				}
			});

			$('#deleteButton').bind('click', function(){
				if(!$('#hasImages input:checkbox:checked').exists()){
					alert('%%LNG_imageManagerNoImagesSelectedForDelete%%');
					return;
				}
				if(confirm('%%LNG_imageManagerConfirmDeleteSelectedImages%%')) {
					var sendPOST = '';
					$('input:checkbox:checked').each(function (){
						if(this.value == '%%image_name%%') { return; }
						sendPOST += '&deleteimages[]=' + escape(this.value);
					});

					$.post('remote.php?remoteSection=imagemanager&w=delete', sendPOST,
							function(json){
								var result = $.evalJSON(json);
								if(result.success){
									for(var i = 0; i < result.successimages.length; i++) {
										var imageName = result.successimages[i];
										imageName = RemoveExtension(imageName);
										$('input:checkbox[value=' + imageName + ']').removeAttr('checked');
										$('input:text[value=' + imageName + ']').parent().hide('slow');
										$('input:text[value=' + imageName + ']').parent().remove();
									}
									display_success('MainMessage', result.message);
									AdminImageManager.CheckDelete();
								}else{
									display_error('MainMessage', result.message);
								}
							});
				}
			});
		});


function RemoveExtension(name){
	var pos = name.lastIndexOf(".");
	if (pos >= 0) {
		var userFriendlyName = name.substr(0, pos);
	}
	else {
		var userFriendlyName = name;
	}

	return userFriendlyName;
}

var OriginalTextValue = '';
var AdminImageManager = {

	noflashTotalUploads: 0,
	percentIncrementNonFlash: 0,
	totalPercentNonFlash: 0,
	totalFieldsNonFlash: 0,
	currentFieldNonFlash: 0,

	GetImageRow: function() {
		return '	<span class="ManageImageBox" id="%%image_id%%" >		<input type="checkbox" id="deleteimages[]" value="%%image_realname%%" class="DeleteImageCheckbox" /><input class="TemplateHeading inPlaceImageBoxDefault" id="%%image_id%%_name" value="%%image_name%%" /><br />		<input type="hidden" id="%%image_id%%_realname" value="%%image_realname%%" />				<div style="width: 202px; height: 156px; margin-top: 5px;">			<a href=\'%%image_url%%\' id="%%image_id%%_url" target="_blank"><img src=\'%%image_url%%\' style=" border: solid 1px #CACACA;"  id="%%image_id%%_image" width="%%image_width%%" height="%%image_height%%" title="Click here to view the full size image" /></a>		</div>				<a href=\'%%image_url%%\' id="%%image_id%%_url" target="_blank"><img width="10" height="11" border="0" src="images/magnify.gif" /></a>		<a href=\'%%image_url%%\' id="%%image_id%%_url" target="_blank">View Full Size</a>		| <a href="index.php?ToDo=downloadImage&image=%%image_realname%%">Download</a><br />				Size: %%image_size%% <br />		Dimensions: %%image_dimensions%%px<br />		<input type="button"  class="SmallButton" id="%%image_id%%_delete" value="Delete this Image" style="width: 150px; margin-top: 4px; margin-bottom: 14px; " />	</span>';
	},

	CheckDelete: function() {
		if (!$('#imagesList .ManageImageBox').exists()) {
			$('#hasImages').hide();
			$('#hasNoImages').show();
			$('#deleteButton').hide();
		} else {
			$('#hasImages').show();
			$('#hasNoImages').hide();
			$('#deleteButton').show();
		}
	},

	CheckAllCheckBoxes: function(checkBox){
		if($('#toggleAllChecks').attr('checked')){
			$('#imagesList input:checkbox').attr('checked', 'checked');
		}else{
			$('#imagesList input:checkbox').removeAttr('checked');
		}
	},

	AddImage: function(name, url, size, displaywidth, displayheight, dimensions, id){
		$('#hasImages').show();
		$('#hasNoImages').hide();
		$('#deleteButton').show();
		var html = AdminImageManager.GetImageRow();

		var userFriendlyName = RemoveExtension(name);

		html = html.replace(/%%image_name%%/g, userFriendlyName);
		html = html.replace(/%%image_realname%%/g, name);
		html = html.replace(/%%image_id%%/g, id);
		html = html.replace(/%%image_url%%/g, url);
		html = html.replace(/%%image_size%%/g, size);
		html = html.replace(/%%image_width%%/g, displaywidth);
		html = html.replace(/%%image_height%%/g, displayheight);
		html = html.replace(/%%image_dimensions%%/g, dimensions);

		$(html).appendTo('#imagesList');

		$('#'+id+'_delete').bind('click',
			function () {
				var idBits = this.id.split('_');
				var id = idBits[0];

				if(confirm('Are you sure you want to delete "' + $('#'+id+'_name').val() +  '"? Click OK to confirm.')) {
					var sendPOST = '';
					sendPOST = 'deleteimages[]=' + $('#'+id+'_realname').val();

					$.post('remote.php?remoteSection=imagemanager&w=delete', sendPOST,
							function(json){
								var result = $.evalJSON(json);
								if(result.success){
									for(var i = 0; i < result.successimages.length; i++) {
										var imageName = result.successimages[i];
										imageName = RemoveExtension(imageName);
										$('input:checkbox[value=' + imageName + ']').removeAttr('checked');
										$('input:text[value=' + imageName + ']').parent().hide('slow');
										$('input:text[value=' + imageName + ']').parent().remove();
									}
									display_success('MainMessage', result.message);
									AdminImageManager.CheckDelete();
								}else{
									display_error('MainMessage', result.message);
								}
							});
				}
			}
		);

		$('#'+id+'_name').bind('mouseover',
			function () {

				if(!$(this).hasClass("inPlaceFieldFocus")) {
					$(this).addClass("inPlaceImageBoxFieldHover");
				}
			}
		);

		$('#'+id+'_name').bind('mouseout',
			function () {
				$(this).removeClass("inPlaceImageBoxFieldHover");
			}
		);

		$('#'+id+'_name').bind('keypress', function(e) {
			if (e.which == null)
				var code = e.keyCode;    // IE
			else if (e.which > 0)
				var code = e.which;	  // All others

			if (code == 32							//	space
				|| (48 <= code && code <= 57)		//	numbers
				|| (65 <= code && code <= 90)		//	lowercase latin letters
				|| (97 <= code && code <= 122)	//	uppercase latin letters
				|| code == 95						//	underscore
				|| code == 13						//	enter
				|| code == 8							//  backspace
				|| (35 <= code && code <= 40 && !e.shiftKey) // home, end, arrows
				|| code == 46						//	delete
				|| code == undefined
				) {
				//	no problem
			} else {
				e.preventDefault();
			}
		});

		$('#'+id+'_name').bind('focus',
			function () {
				$('.inPlaceFieldFocus').each(function(){
					cancelEditName($(this));
					$(this).removeClass('inPlaceFieldFocus');
				});
				$(this).removeClass("inPlaceImageBoxFieldHover");
				$(this).addClass("inPlaceFieldFocus");
				OriginalTextValue = this.value;
				this.select();
				$('<div style="background-color: #F9F9F9; width: 205px; position: absolute; padding: 5px; top: 30px; left: 2px;" id="EditNameButtons"><input type="button" class="FormButton" name="saveEdit" value="%%LNG_Save%%"  style="float: right;" onclick="saveEditName($(\'#' + this.id + '\'));" /><input type="button" class="FormButton" name="cancelEdit" value="%%LNG_Cancel%%" style="float: left;"  onclick="cancelEditName($(\'#' + this.id + '\'));" /> </div>').insertAfter(this);
			}
		);


		if ($.browser.mozilla) {
			var event = "keypress";
		} else {
			var event = "keydown";
		}

		$('#'+id+'_name').bind(event, function(e) {
			if (e.keyCode == 13) {
				$('#'+id+'_name').blur();
			}
		});
	}
};

function saveEditName(field){
	$(field).attr('disabled', true);

	var idBits = field.attr('id');
	idBits = idBits.split('_');
	var id = idBits[0];
	$('#EditNameButtons').remove();

	field.removeClass("inPlaceFieldFocus");
	if(field.val() != OriginalTextValue){
		$.post('remote.php?remoteSection=imagemanager&w=rename', 'fromName=' + escape($('#' + id + '_realname').val()) + '&toName=' + escape(field.val()),
			function(json){
				var result = $.evalJSON(json);
				if(result.success){
					var message = '%%LNG_fileRenamedSuccess%%';
					message = message.replace('%from%', OriginalTextValue);
					message = message.replace('%to%', result.newname);
					display_success("MainMessage", message);

					$('#' + id + '_image').attr('src', result.newurl);
					$('#' + id + '_url').attr('href', result.newurl);
					$('#' + id + '_realname').val(result.newrealname);
				}else{
					display_error("MainMessage", '%%LNG_fileRenamedError%% ' + result.message);
					$('#'+id+'_name').val(OriginalTextValue);
				}
			});
	}

	$(field).attr('disabled', false);
}


function cancelEditName(field){
	$(field).val(OriginalTextValue);
	$(field).removeClass("inPlaceFieldFocus");
	$('#EditNameButtons').remove();
}

function ChangeImageManagerPaging(object, pagenumber) {
	pagingId = object.selectedIndex;
	pagingamount = object[pagingId].value;
	window.location = 'index.php?ToDo=manageImages&page=' + pagenumber + '&perpage='+ pagingamount;
}


function ChangeImageManagerSorting(object, pagenumber) {
	pagingId = object.selectedIndex;
	var sortby = object[pagingId].value;
	window.location = 'index.php?ToDo=manageImages&page=' + pagenumber + '&sortby='+ sortby;
}

</script>

<div class="BodyContainer">
<table class="OuterPanel">
	<tr>
		<td class="Intro">

		<h2 class="Heading1">%%LNG_ManageImages%%</h2>
		<p>%%LNG_ManageImagesIntro%%</p>

		<div id="MainMessage">
			%%GLOBAL_Message%%
		</div>

		<p>
			<span id="spanButtonPlaceholder"></span>
			<input id="btnUpload" type="button" class="SmallButton" value="%%LNG_imageManagerUploadImages%%" style="width: 130px;" />&nbsp;<input id="deleteButton" type="button" value="%%LNG_imageManagerDeleteSelected%%"  class="SmallButton"  style="display: " /><br /><br />
		</p>
</td>
</tr><tr><td>

<div style="display: none" id="ProgressWindow">
<div id="ProgressBarDiv" style="text-align: center;"><br/><span id="ProgressBarText" class="ProgressBarText">%%LNG_imageManagerUploadInProgress%%</span><br/><br/><br/>
	<div style="border: 1px solid #ccc; width: 300px; padding: 0px; margin: 0 auto; position: relative;">
		<div class="progressBarPercentage" style="margin: 0; padding: 0; background: url('images/progressbar.gif') no-repeat; height: 20px; width: 0%; ">
			&nbsp;
		</div>
		<div style="position: absolute; top: 0px; left: 0; text-align: center; width: 300px; font-weight: bold;line-height:1.5;color:#333333;font-family:Tahoma;font-size:11px;" class="progressPercent">&nbsp;</div>
	</div>
	<span id="progressBarStatus" class="progressBarStatus" style="text-align: center; font-size: 10px; color: gray; padding-top: 5px;">&nbsp;</span>
	<br />
	<br/>
	<br/>
</div>

</div><!-- End #ProgressWindow -->

<div id="hasImages" style="display: %%GLOBAL_hideImages%%;">
<div style="">
	<div style="float: right">%%GLOBAL_paging%%</div>
	<div style="float: right; padding-bottom: 7px;">
		<select name="PerPage" class="Field" style="width: 180px;" onChange="ChangeImageManagerPaging(this, '%%GLOBAL_PageNumber%%');">
			<option value="10" %%GLOBAL_PerPage10Selected%%>%%LNG_imageManager10PerPage%%</option>
			<option value="20" %%GLOBAL_PerPage20Selected%%>%%LNG_imageManager20PerPage%%</option>
			<option value="50" %%GLOBAL_PerPage50Selected%%>%%LNG_imageManager50PerPage%%</option>
			<option value="100" %%GLOBAL_PerPage100Selected%%>%%LNG_imageManager100PerPage%%</option>
			<option value="0" %%GLOBAL_PerPageAllSelected%%>%%LNG_imageManagerShowAllImages%%</option>
		</select>
		<select name="SortBy" class="Field" style="width: 150px;" onChange="ChangeImageManagerSorting(this, '%%GLOBAL_PageNumber%%');">
			<option value="name.asc" %%GLOBAL_SortNameAsc%%>%%LNG_SortNameAsc%%</option>
			<option value="name.desc" %%GLOBAL_SortNameDesc%%>%%LNG_SortNameDesc%%</option>

			<option value="modified.asc" %%GLOBAL_SortModifiedAsc%%>%%LNG_SortDateAsc%%</option>
			<option value="modified.desc" %%GLOBAL_SortModifiedDesc%%>%%LNG_SortDateDesc%%</option>

			<option value="size.asc" %%GLOBAL_SortSizeAsc%%>%%LNG_SortFilesizeAsc%%</option>
			<option value="size.desc" %%GLOBAL_SortSizeDesc%%>%%LNG_SortFilesizeDesc%%</option>
		</select>
	</div>
</div>
<div style="clear:both;"></div>
	<table class="Panel" style="margin:0px;">
			<tr>
				<td class="Heading2" colspan='2'>
					<input type="checkbox" name="toggleAllChecks" id="toggleAllChecks" onclick="AdminImageManager.CheckAllCheckBoxes(this);" style="margin: 3px 0 0 0 ; float: left;" />
					<label for="toggleAllChecks" style="display: block; padding-top: 4px; float: left; padding-left: 4px;">%%GLOBAL_ImagesTitle%%</label>
				</td>
			</tr>
			<tr>
				<td align="right" style=" padding-left: 10px;" colspan='2'>

				</td>
			</tr>
			<tr>
				<td style="padding:5px 5px 5px 10px;" colspan='2'>



	<div id="imagesList">
		<script type="text/javascript">
			%%GLOBAL_imagesList%%
		</script>
	</div><!-- end #imagesList -->

	</td>
			</tr>
				<tr>
				<td align="right" style=" padding-left: 10px;" colspan='2'>
					<select name="PerPage" class="Field" style="width: 180px;" onChange="ChangeImageManagerPaging(this, '%%GLOBAL_PageNumber%%');">
						<option value="10" %%GLOBAL_PerPage10Selected%%>%%LNG_imageManager10PerPage%%</option>
						<option value="20" %%GLOBAL_PerPage20Selected%%>%%LNG_imageManager20PerPage%%</option>
						<option value="50" %%GLOBAL_PerPage50Selected%%>%%LNG_imageManager50PerPage%%</option>
						<option value="100" %%GLOBAL_PerPage100Selected%%>%%LNG_imageManager100PerPage%%</option>
						<option value="0" %%GLOBAL_PerPageAllSelected%%>%%LNG_imageManagerShowAllImages%%</option>
					</select>
					<select name="SortBy" class="Field" style="width: 150px;" onChange="ChangeImageManagerSorting(this, '%%GLOBAL_PageNumber%%');">
						<option value="name.asc" %%GLOBAL_SortNameAsc%%>%%LNG_SortNameAsc%%</option>
						<option value="name.desc" %%GLOBAL_SortNameDesc%%>%%LNG_SortNameDesc%%</option>

						<option value="modified.asc" %%GLOBAL_SortModifiedAsc%%>%%LNG_SortDateAsc%%</option>
						<option value="modified.desc" %%GLOBAL_SortModifiedDesc%%>%%LNG_SortDateDesc%%</option>

						<option value="size.asc" %%GLOBAL_SortSizeAsc%%>%%LNG_SortFilesizeAsc%%</option>
						<option value="size.desc" %%GLOBAL_SortSizeDesc%%>%%LNG_SortFilesizeDesc%%</option>
					</select>
				</td>
			</tr>
	 </table><br />
	 %%GLOBAL_paging%%
</div>

<div id="hasNoImages" style="display: %%GLOBAL_hideHasNoImages%%; text-align: center;">
%%LNG_PromptToCreateImage%%
</div>

</td></tr></table>
</div>

<div style="display:none;" id="MultiUploadDialogTemplate">
	<div class="ModalTitle">%titletext%</div>
	<div class="ModalContent">
		<div class="MultiUploadDialogContent">
			<div class="UploadDialog">
				<p>%introtext%</p>
				<div class="GrowingUploader">
					<input type="file" name="Filedata" width="300" class="Button MultiUploadDialogInput" /> <a href="#">%cleartext%</a>
				</div>
			</div>
			<div class="ProgressIndicator" style="display:none;">
				<p class="ProgressMessage"></p>
				<div class="ProgressBar">
					<div class="ProgressBarColour">&nbsp;</div>
					<div class="ProgressBarText"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="ModalButtonRow">
		<div class="MultiUploadDialogButtons">
			<div class="FloatLeft">
				<input type="button" class="CloseButton FormButton" value="%closetext%" />
			</div>
			<input type="button" class="Submit FormButton" value="%submittext%" />
		</div>
	</div>
</div><!-- end #MultiUploadDialogTemplate -->
