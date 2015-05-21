<div class="ModalTitle">
	%%GLOBAL_ExportName%%
</div>
<div class="ModalContent">
	<div id="exportIntro" style="display: %%GLOBAL_HideExportIntro%%">
		<p>
			%%GLOBAL_ExportIntro%%
		</p>

		<table border="0">
			<tr>
				<td width="1"><img src="images/%%GLOBAL_ExportIcon%%" height="16" width="16" hspace="5" alt="" /></td>
				<td><a href="#" onclick="StartAjaxExport(); return false;"  style="color:#005FA3; font-weight:bold">%%GLOBAL_ExportGenerate%%</a></td>
			</tr>
		</table>

		<div style="display: %%GLOBAL_DisplayAutoExport%%">
			<p><strong>%%LNG_SchedulingAutomaticUpdates%%</strong></p>
			<p>%%LNG_AutomaticExportIntro%%</p>
			<p>%%LNG_AutomaticExportIntro2%%</p>
			<p style="padding-left: 25px">
				<input type="text" class="Field300" onclick="this.select()" readonly="readonly" value="%%GLOBAL_ExportUrl%%" />
			</p>
		</div>
	</div>
	<div id="exportStatus" style="display: none;">
		<p>
			%%GLOBAL_ExportGeneratingIntro%%
		</p>
		<div style="border: 1px solid #ccc; width: 300px; padding: 0px; margin: 0 auto; position: relative;">
			<div id="ProgressBarPercentage" style="margin: 0; padding: 0; background: url(images/progressbar.gif) no-repeat; height: 20px; width: 0%;">
				&nbsp;
			</div>
			<div style="position: absolute; top: 0; left: 0; text-align: center; width: 300px; font-weight: bold;" id="ProgressPercent">&nbsp;</div>
		</div>
		<div id="ProgressBarStatus" style="text-align: center; font-size: 11px; font-family: Tahoma;">%%GLOBAL_ExportGenerating%%</div>
	</div>
	<div id="exportComplete" style="display: none;">
		<p>
			%%GLOBAL_ExportGeneratedIntro%%
		</p>
		<table border="0">
			<tr>
				<td width="1"><img src="images/save.gif" height="16" width="16" hspace="5" alt="" /></td>
				<td><a href="#" onclick="DownloadAjaxExport(); return false;"  style="color:#005FA3; font-weight:bold">%%GLOBAL_ExportDownload%%</a></td>
			</tr>
		</table>
	</div>
	<div id="exportNoProducts" style="display: %%GLOBAL_HideNoProducts%%;">
		<p>
			%%GLOBAL_ExportIntro%%
		</p>
		<table border="0">
			<tr>
				<td width="1" valign="top"><img src="images/error.gif" height="16" width="16" hspace="5" alt="" /></td>
				<td style="font-weight: bold;">%%LNG_ExportNoData%%</td>
			</tr>
		</table>
		<br />
	</div>
</div>
<div class="ModalButtonRow">
	<input type="button" value="%%LNG_Close%%" onclick="$.iModal.close()" class="SubmitButton" />
</div>
<script type="text/javascript">
	function StartAjaxExport() {
		$('#exportStatus').show();
		$('#exportIntro').hide();
		if(g('ExportFrame')) {
			$('#ExportFrame').remove();
		}
		$('#exportStatus').append('<iframe src="index.php?ToDo=AjaxExport&exportsess=%%GLOBAL_ExportSessionId%%&action=Export" border="0" frameborder="0" height="1" width="1" id="ExportFrame"></iframe>');
	}

	function AjaxExportError(msg) {
	//	tb_remove();
		alert(msg);
	}

	function UpdateAjaxExportProgress(percentage) {
		$('#ProgressBarPercentage').css('width', parseInt(percentage) + "%");
		$('#ProgressPercent').html(parseInt(percentage) + "%");
	}

	function AjaxExportComplete() {
		$('#exportStatus').hide();
		$('#exportComplete').show();
	}

	function CancelAjaxExport() {
		if($('#exportStatus').css('display') != "none") {
			window.location = 'index.php?ToDo=AjaxExport&exportsess=%%GLOBAL_ExportSessionId%%&action=CancelExport';
		}
	}

	function DownloadAjaxExport() {
		$.iModal.close();
		window.location = 'index.php?ToDo=AjaxExport&exportsess=%%GLOBAL_ExportSessionId%%&action=DownloadExport';
	}
</script>