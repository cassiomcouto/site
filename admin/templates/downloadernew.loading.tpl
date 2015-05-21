<center><fieldset>
	<legend id="legendText">%%GLOBAL_DownloadPleaseWait%%</legend>
	<div id="contentDiv">
		<img src="images/loadingAnimation.gif" >
	</div>
</fieldset></center>
<script type="text/javascript">// <![CDATA[
window.setTimeout(function() {
	$.ajax({
		url: 'remote.php',
		data: 'w=downloadtemplatefile&template=%%GLOBAL_TemplateId%%',
		type: 'POST',
		dataType: 'xml',
		success: function(data) {
			tb_remove();
			if($('status', data).text() == 1) {
				window.location = 'index.php?ToDo=changeTemplate&template=%%GLOBAL_TemplateId%%&color=%%GLOBAL_TemplateColor%%'
			}
			else {
				alert($('message', data).text());
			}
		}
	});
}, 1000);
//]]></script>
