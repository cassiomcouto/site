<table class="GridPanel SortableGrid" style="width:100%" id="optionGrid">
	<tr>
		<td class="Heading2 VariationSpanRow" colspan="%%GLOBAL_ColSpan%%"><span style="float: right; margin-right: 5px;"><label><input type="checkbox" id="showFilter" %%GLOBAL_ShowFilterChecked%% /> <span>%%LNG_ShowFilter%%</span></label></span>%%LNG_FilterAndUpdateHeading%%</td>
	</tr>
	<tr>
		<td colspan="%%GLOBAL_ColSpan%%" class="VariationSpanRow">
			<div id="filterBlock" style="display: %%GLOBAL_ShowVariationFilter%%">
				<table style="width: 100%;">
					<tr valign="top">
						<td style="width:50%;">
							<fieldset id="filterForm">
								<legend>%%LNG_SearchFilter%%</legend>
								<div id="optionList">
									%%GLOBAL_FilterOptions%%
									<label>&nbsp;</label>
									<input class="SubmitButton" type="button" value="%%LNG_ApplyFilter%%" id="applyFilter" class="Field100" /> %%LNG_ResetFilter%%
								</div>
							</fieldset>
						</td>
						<td>
							<fieldset id="bulkUpdateForm">
								<input type="hidden" name="filterOptions" value="%%GLOBAL_FilterOptionsQuery%%" />

								<legend>%%LNG_BulkUpdate%%</legend>

								<label>%%LNG_CanBePurchased%%</label>
								<select name="updatePurchaseable">
									<option value="noupdate">%%LNG_DoNotUpdate%%</option>
									<option value="reset">%%LNG_ResetField%%</option>
									<option value="yes">%%LNG_SYes%%</option>
									<option value="no">%%LNG_SNo%%</option>
								</select>
								<br />

								<label>%%LNG_VariationPrice%%:</label>
								<select name="updatePriceDiff" id="updatePriceDiff" onchange="if (this.selectedIndex > 1) { $(this).next('span').show(); $(this).next('span').find('input').focus(); } else { $(this).next('span').hide(); }">
									<option value="noupdate">%%LNG_DoNotUpdate%%</option>
									<option value="reset">%%LNG_ResetField%%</option>
									<option value="add">%%LNG_VariationAdd%%</option>
									<option value="subtract">%%LNG_VariationSubtract%%</option>
									<option value="fixed">%%LNG_VariationFixed%%</option>
								</select>
								<span style='display: none'>
									%%GLOBAL_CurrencyTokenLeft%% <input name="updatePrice" id="updatePrice" type='text' class='NumberField'/> %%GLOBAL_CurrencyTokenRight%%
								</span>
								<br />

								<label>%%LNG_VariationWeight%%:</label>
								<select name="updateWeightDiff" id="updateWeightDiff" onchange="if (this.selectedIndex > 1) { $(this).next('span').show(); $(this).next('span').find('input').focus(); } else { $(this).next('span').hide(); }">
									<option value="noupdate">%%LNG_DoNotUpdate%%</option>
									<option value="reset">%%LNG_ResetField%%</option>
									<option value="add">%%LNG_VariationAdd%%</option>
									<option value="subtract">%%LNG_VariationSubtract%%</option>
									<option value="fixed">%%LNG_VariationFixed%%</option>
								</select>
								<span style='display: none'>
									<input name="updateWeight" id="updateWeight" type='text' class='NumberField' /> %%GLOBAL_WeightMeasurement%%
								</span>
								<br />

								<label>%%LNG_Image%%:</label>
								<input type="file" name="updateImage" id="updateImage" />
								<br />

								<label>%%LNG_DeleteImages%%</label>
								<input type="checkbox" name="updateDelImages" id="updateDelImages" value="1" style="width: auto;" />
								%%LNG_YesDeleteImages%%
								<br />

								<div class="VariationStockColumn" style="display: %%GLOBAL_HideInv%%">
									<label>%%LNG_CurrentStockLevel%%:</label>
									<input class="NumberField" name="updateStockLevel" id="updateStockLevel" type="text" />
									<br />
								</div>

								<div class="VariationStockColumn" style="display: %%GLOBAL_HideInv%%">
									<label>%%LNG_LowStockLevel1%%:</label>
									<input class="NumberField" name="updateLowStockLevel" id="updateLowStockLevel" type="text" />
									<br />
								</div>

								<label>&nbsp;</label>
								<input class="SubmitButton" type="button" value="%%LNG_ApplyToAll%%" id="bulkUpdate" />
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="%%GLOBAL_ColSpan%%" class="VariationSpanRow">
			<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
				<td align="right" class="PagingNav" style="padding:6px 0px 6px 0px; width: 100%;">
					%%GLOBAL_Nav%%
				</td>
			</table>
		</td>
	</tr>
	<tr class="Heading3">
		<td><span class="HelpText" onmouseout="HideQuickHelp(this);" onmouseover="ShowQuickHelp(this, '%%LNG_EnableDisableAll%%', '%%LNG_EnableDisableAllHelp%%');"><input type='checkbox' checked='checked' onclick="$('#optionGrid').find('input[type=checkbox]').attr('checked', this.checked)" /></span></td>
		%%GLOBAL_HeaderRows%%
		<td>%%LNG_SKU%%</td>
		<td><span class="HelpText" onmouseout="HideQuickHelp(this);" onmouseover="ShowQuickHelp(this, '%%LNG_VariationPrice%%', '%%LNG_VariationPriceHelp%%');">%%LNG_VariationPrice%%</span></td>
		<td><span class="HelpText" onmouseout="HideQuickHelp(this);" onmouseover="ShowQuickHelp(this, '%%LNG_VariationWeight%%', '%%LNG_VariationWeightHelp%%');">%%LNG_VariationWeight%%</span></td>
		<td><span class="HelpText" onmouseout="HideQuickHelp(this);" onmouseover="ShowQuickHelp(this, '%%LNG_Image%%', '%%LNG_VariationImageHelp%%');">%%LNG_Image%%</span></td>
		<td style="display:%%GLOBAL_HideInv%%" class="VariationStockColumn"><span class="HelpText" onmouseout="HideQuickHelp(this);" onmouseover="ShowQuickHelp(this, '%%LNG_StockLevel%%', '%%LNG_StockLevelHelp%%');">%%LNG_StockLevel%%</span></td>
		<td style="display:%%GLOBAL_HideInv%%" class="VariationStockColumn"><span class="HelpText" onmouseout="HideQuickHelp(this);" onmouseover="ShowQuickHelp(this, '%%LNG_LowStockLevel%%', '%%LNG_LowStockLevelHelp%%');">%%LNG_LowStockLevel%%</span></td>
	</tr>
	%%GLOBAL_VariationRows%%
	<tr>
		<td colspan="%%GLOBAL_ColSpan%%" class="VariationSpanRow">
			<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
				<td align="right" class="PagingNav" style="padding:6px 0px 6px 0px; width: 100%;">
					%%GLOBAL_Nav%%
				</td>
			</table>
		</td>
	</tr>
</table>
<script type="text/javascript">
	$("#applyFilter").click(function() {
		var formData = $('#filterForm :input').serializeArray();
		var showInv = '0';
		if ($('#prodInvTrack_2').attr('checked')) {
			showInv = 1;
		}

		$(this).parents('.GridContainer').load('remote.php?w=getVariationCombinations&productId=%%GLOBAL_VProductId%%&productHash=%%GLOBAL_VProductHash%%&v=%%GLOBAL_VariationId%%&inv=' + showInv, formData, function() {
			BindAjaxGridSorting();
			BindGridRowHover();
		});
	});

	$("#bulkUpdate").click(function() {
		var formData = $('#bulkUpdateForm :input').serialize();
		var showInv = '0';
		if ($('#prodInvTrack_2').attr('checked')) {
			showInv = 1;
		}

		// validate the price
		if ($("#updatePriceDiff").attr('selectedIndex') > 1) {
			if (isNaN(priceFormat($("#updatePrice").val())) || $("#updatePrice").val() == '' || $("#updatePrice").val() < 0) {
				alert("%%LNG_UpdateEnterValidPrice%%");
				$("#updatePrice").focus();
				return;
			}
		}

		// validate the weight
		if ($("#updateWeightDiff").attr('selectedIndex') > 1) {
			if (isNaN($("#updateWeight").val()) || $("#updateWeight").val() == '' || $("#updateWeight").val() < 0) {
				alert("%%LNG_UpdateEnterValidWeight%%");
				$("#updateWeight").focus();
				return;
			}
		}

		// validate stock levels
		if (showInv) {
			if (isNaN($("#updateStockLevel").val()) || $("#updateStockLevel").val() < 0) {
				alert("%%LNG_UpdateEnterValidStock%%");
				$("#updateStockLevel").focus();
				return;
			}

			if (isNaN($("#updateLowStockLevel").val()) || $("#updateLowStockLevel").val() < 0) {
				alert("%%LNG_UpdateEnterValidLowStock%%");
				$("#updateLowStockLevel").focus();
				return;
			}
		}

		$('#LoadingIndicator').show();

		$.ajaxFileUpload({
			url: 'remote.php?w=bulkUpdateVariations&productId=%%GLOBAL_VProductId%%&productHash=%%GLOBAL_VProductHash%%&v=%%GLOBAL_VariationId%%&inv=' + showInv + '&' + formData,
			secureuri: false,
			fileElementId: 'updateImage',
			dataType: 'json',
			success: function(data) {
				$("#bulkUpdate").parents('.GridContainer').html(data.tableData);

				BindAjaxGridSorting();
				BindGridRowHover();
			}
		});

		$('#LoadingIndicator').hide();

		return;
	});

	$("#showFilter").change(function() {
		$("#filterBlock").slideToggle('normal');
		SetCookie('showVariationFilter', $(this).attr('checked'), 365);
	});

	function resetFilter() {
		$("#optionList select").each(function() {
			$(this).find('option').removeAttr('selected');
			$(this).find('option:first').attr('selected', 'selected');
		});

		$("#applyFilter").click();
	}
</script>
