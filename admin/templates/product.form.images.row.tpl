<div class="productImagesListItem">
	<table cellspacing="0" class="GridPanel">
		<tbody>
			<tr class="GridRow" onmouseover="$(this).addClass('GridRowOver').removeClass('GridRow');" onmouseout="$(this).addClass('GridRow').removeClass('GridRowOver');">
				<td class="productImageCheck"><input type="checkbox" /></td>
				<td class="productImageThumbDisplay" style="width:%%GLOBAL_productImage_thumbnailWidth%%px;">
					<a style="width:%%GLOBAL_productImage_thumbnailWidth%%px; height:%%GLOBAL_productImage_thumbnailHeight%%px;" title="%%LNG_ClickToShowFullSizeImage%%"></a>
				</td>
				<td class="productImageDescription"><textarea rows="4"></textarea><div style="display:none;"><input type="button" class="productImageDescriptionSave" value="%%LNG_Save%%" /> <input type="button" class="productImageDescriptionCancel" value="%%LNG_Cancel%%" /></div></td>
				<td class="productImageBaseThumb"><input type="radio" name="productImageBaseThumb" /></td>
				<td class="productImageAction"><a href="#" class="productImageDelete">%%LNG_Delete%%</a></td>
			</tr>
		</tbody>
	</table>
</div>
