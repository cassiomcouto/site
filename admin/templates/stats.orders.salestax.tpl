<a name="productsByInventoryAnchor"></a>
<div style="text-align:right">
	<div style="padding-bottom:10px">
		%%LNG_ItemsPerPage%%:
		<select onchange="ChangeTaxByDatePerPage(this.options[this.selectedIndex].value)">
			<option %%GLOBAL_IsShowPerPage5%% value="5">5</option>
			<option %%GLOBAL_IsShowPerPage10%% value="10">10</option>
			<option %%GLOBAL_IsShowPerPage20%% value="20">20</option>
			<option %%GLOBAL_IsShowPerPage30%% value="30">30</option>
			<option %%GLOBAL_IsShowPerPage50%% value="50">50</option>
			<option %%GLOBAL_IsShowPerPage100%% value="100">100</option>
		</select>
	</div>
	%%GLOBAL_Paging%%
</div>
<br />
<table width="100%" border=0 cellspacing=1 cellpadding=5 class="text">
	<tr class="Heading3">
		<td align="left">
			%%LNG_Period%% &nbsp;
			%%GLOBAL_SortLinksPeriod%%
		</td>
		<td align="left">
			%%LNG_TaxType%% &nbsp;
			%%GLOBAL_SortLinksTaxType%%
		</td>
		<td align="center">
			%%LNG_Rate%% &nbsp;
			%%GLOBAL_SortLinksTaxRate%%
		</td>
		<td align="center">
			%%LNG_NumberOfOrders%% &nbsp;
			%%GLOBAL_SortLinksNumOrders%%
		</td>
		<td align="left" width="100">
			%%LNG_TaxAmount%% &nbsp;
			%%GLOBAL_SortLinksTaxAmount%%
		</td>
	</tr>
	%%GLOBAL_TaxGrid%%
</table>