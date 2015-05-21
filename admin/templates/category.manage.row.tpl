				<li id="ele-%%GLOBAL_CatId%%" class="SortableRow">
					<table class="GridPanel" cellspacing="0" cellpadding="0" border="0" style="width:100%;">
						<tr class="GridRow" onmouseover="this.className='GridRowOver'" onmouseout="this.className='GridRow'">
							<td width="1"><input type="checkbox" name="categories[%%GLOBAL_CatId%%]" /></td>
							<td class="DragMouseDown sort-handle">%%GLOBAL_CatName%%</td>
							<td width="80" class="HideOnDrag">%%GLOBAL_Products%%</td>
							<td width="80" class="HideOnDrag">%%GLOBAL_CatVisible%%</td>
							<td width="108" class="HideOnDrag">
								%%GLOBAL_ViewLink%%&nbsp;&nbsp;&nbsp;
								%%GLOBAL_NewLink%%&nbsp;&nbsp;&nbsp;
								%%GLOBAL_EditLink%%&nbsp;&nbsp;&nbsp;
								%%GLOBAL_DeleteLink%%
							</td>
						</tr>
					</table>
					%%GLOBAL_SubCats%%
				</li>