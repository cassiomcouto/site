<div class="ModalTitle">
	%%LNG_OrderNotesPopupHeading%%
</div>
<div class="ModalContent">
	<p class="MessageBox MessageBoxInfo">
		%%LNG_OrderNotesPopupIntro%%
	</p>

	<form action="" id="notesForm">
		<input type="hidden" id="orderId" name="orderId" value="%%GLOBAL_OrderID%%" />

		<table class="GridPanel">
			<tr class="Heading3">
				<td>%%LNG_OrderComments%%</td>
			</tr>
			<tr>
				<td>
					<textarea id="ordcustmessage" name="ordcustmessage" rows="8" style="width:98%;">%%GLOBAL_OrderCustomerMessage%%</textarea>
				</tr>
			</tr>
			<tr class="Heading3">
				<td>%%LNG_StaffNotes%%</td>
			</tr>
			<tr>
				<td>
					<textarea id="ordnotes" name="ordnotes" rows="8" style="width:98%;">%%GLOBAL_OrderNotes%%</textarea>
				</td>
			</tr>
		</table>
	</form>
</div>
<div class="ModalButtonRow">
	<div class="FloatLeft">
		<img src="images/loading.gif" alt="" style="vertical-align: middle; display: none;" class="LoadingIndicator" />
		<input type="button" class="CloseButton FormButton" value="%%LNG_Cancel%%" onclick="$.modal.close();" />
	</div>
	<input type="button" name="SaveNotesButton" class="Submit" value="%%LNG_Save%%" onclick="Order.SaveNotes('%%GLOBAL_ThankYouID%%')" />
</div>

<script type="text/javascript">
	lang.OrderCommentsDefault = '%%LNG_OrderCommentsDefault%%';
	lang.OrderNotesDefault = '%%LNG_OrderNotesDefault%%';

	function ShowOrderCommentsDefault()
		{
			$('#ordcustmessage')
				.val(lang.OrderCommentsDefault)
				.data('usingDefault', 1)
				.addClass('OrderDefaultField')
				.attr('name', 'ordcustmessage_default')
			;
		}

		function ShowOrderNotesDefault()
		{
			$('#ordnotes')
				.val(lang.OrderNotesDefault)
				.data('usingDefault', 1)
				.addClass('OrderDefaultField')
				.attr('name', 'ordnotes_default')
			;
		}

		if(!$('#ordcustmessage').val()) {
			ShowOrderCommentsDefault();
			$('#ordcustmessage')
				.focus(function() {
					if($(this).data('usingDefault') != 1) {
						return;
					}
					$(this)
						.val('')
						.attr('name', 'ordcustmessage')
						.removeClass('OrderDefaultField')
					;
				})
				.blur(function() {
					if(!$(this).val()) {
						ShowOrderCommentsDefault();
					}
					else {
						$(this)
							.data('usingDefault', 0)
						;
					}
				})
			;
		}

		if(!$('#ordnotes').val()) {
			ShowOrderNotesDefault();
			$('#ordnotes')
				.focus(function() {
					if($(this).data('usingDefault') != 1) {
						return;
					}
					$(this)
						.val('')
						.attr('name', 'ordnotes')
						.removeClass('OrderDefaultField')
					;
				})
				.blur(function() {
					if(!$(this).val()) {
						ShowOrderNotesDefault();
					}
					else {
						$(this)
							.data('usingDefault', 0)
						;
					}
				})
			;
		}
</script>