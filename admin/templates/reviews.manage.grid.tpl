			<table class="GridPanel SortableGrid" cellspacing="0" cellpadding="0" border="0" id="IndexGrid" style="width:100%;">
				<tr align="right">
					<td colspan="9" style="padding:6px 0px 6px 0px" class="PagingNav">
						%%GLOBAL_Nav%%
					</td>
				</tr>
			<tr class="Heading3">
				<td align="center" style="width:18px"><input type="checkbox" onclick="ToggleDeleteBoxes(this.checked)"></td>
				<td>&nbsp;</td>
				<td style="width:25%">
					%%LNG_ReviewTitle%% &nbsp;
					%%GLOBAL_SortLinksReview%%
				</td>
				<td>
					%%LNG_Product%% &nbsp;
					%%GLOBAL_SortLinksName%%
				</td>
				<td>

					%%LNG_Rating%% &nbsp;
					%%GLOBAL_SortLinksRating%%
				</td>
				<td>
					%%LNG_PostedBy%% &nbsp;
					%%GLOBAL_SortLinksBy%%
				</td>
				<td>
					%%LNG_Date%% &nbsp;
					%%GLOBAL_SortLinksDate%%
				</td>
				<td style="width:70px">
					%%LNG_Status%% &nbsp;
					%%GLOBAL_SortLinksStatus%%
				</td>
				<td style="width:80px">
					%%LNG_Action%%
				</td>
			</tr>
			%%GLOBAL_ReviewGrid%%
			<tr align="right">
				<td colspan="9" style="padding:6px 0px 6px 0px" class="PagingNav">
					%%GLOBAL_Nav%%
				</td>
			</tr>
		</table>
		<a href="?searchQuery=%%GLOBAL_Query%%&amp;page=%%GLOBAL_Page%%%%GLOBAL_SortURL%%" id="ReviewSortURL"></a>