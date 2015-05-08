	<script type="text/javascript">
		ShowLoadingIndicator();
		window.onload = function() {
			HideLoadingIndicator();
		};

		function quickToggle(element)
		{
			var image = element.childNodes[0];
			if(image.src.indexOf('tick')==-1) {
				var confirmMessage = "%%LNG_CategoryVisibleConfirmation%%";
			} else {
				var confirmMessage = "%%LNG_CategoryInvisibleConfirmation%%";
			}
			if(confirm(confirmMessage)) {
				$.ajax({
					url: element.href + '&ajax=1',
					dataType: 'script',
					success: function(response) {

						if(status == 0) {
							display_error('CategoriesStatus', '%%LNG_ErrCategoryVisibilityNotChanged%%');
						}
						else {
							display_success('CategoriesStatus', message);
						}
					}
				});
			}
		}

		function ToggleVisibilityIcon(elementID, what, visible)
		{
			var element = document.getElementById(elementID);
			if(element.childNodes.length == 1 && element.childNodes[0].tagName == "IMG") {
				var image = element.childNodes[0];

				// Element was ticked, now should not be
				if(visible == 0) {
					element.href = element.href.replace(what+'=0', what+'=1');
					image.src = image.src.replace('tick', 'cross');
				}
				else {
					element.href = element.href.replace(what+'=1', what+'=0');
					image.src = image.src.replace('cross', 'tick');
				}
			}
		}
	</script>

	<div class="BodyContainer">
	<table class="OuterPanel">
	<tr>
		<td class="Heading1">%%LNG_ViewCategories%%</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td class="Intro">
					<p>
					%%LNG_ManageCatIntro%%
					</p>
					%%GLOBAL_Message%%
				</td>
			</tr>
			<tr><td><div id="CategoriesStatus" style="margin-bottom: 10px;"></div></td></tr>
			<tr>
				<td class="Intro" style="padding-bottom:10px">
					<input type="button" onclick="document.location.href='index.php?ToDo=createCategory'" name="createNewCategory" value="%%LNG_CreateCategory%%..." class="Button"> &nbsp;<input type="button" name="IndexDeleteButton" value="%%LNG_DeleteSelected%%" id="IndexDeleteButton" class="SmallButton" onclick="ConfirmDeleteSelected()" %%GLOBAL_DisableDelete%% />
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 10px;">
			<form method="post" id="frmCategories" onSubmit="return false" action="index.php?ToDo=deleteCategory">
				<table class="GridPanel SortablePanel" cellspacing="0" cellpadding="0" border="0" style="width:100%; display: %%GLOBAL_DisplayGrid%%">
					<tr class="Heading3">
						<td style="padding-left: 5px;" width="1"><input type="checkbox" onclick="ToggleDeleteBoxes(this.checked)"></td>
						<td>%%LNG_CategoryName%%</td>
						<td width="80">%%LNG_CategoryProducts%%</td>
						<td width="120">
							<span onmouseover="ShowQuickHelp(this, '%%LNG_VisibleInMenuList%%', '%%LNG_CategoryVisibilityHelp%%');" onmouseout="HideQuickHelp(this);" class="HelpText">%%LNG_VisibleInMenuList%%</span>
						</td>
						<td width="108">
							%%LNG_Action%%
						</td>
					</tr>
				</table>
				<ul class="SortableList" id="CategoryList">
					%%GLOBAL_CategoryGrid%%
				</ul>
			</form>
		</td>
	</tr>
	</table>
	</div>
	<script type="text/javascript" src="../javascript/jquery/plugins/interface.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript" src="../javascript/jquery/plugins/inestedsortable.js?%%GLOBAL_JSCacheToken%%"></script>
	<script type="text/javascript">

		function ToggleDeleteBoxes(Status)
		{
			var fp = document.getElementById("frmCategories").elements;

			for(i = 0; i < fp.length; i++)
				fp[i].checked = Status;
		}

		function ConfirmDeleteSelected()
		{
			var fp = document.getElementById("frmCategories").elements;
			var c = 0;

			for(i = 0; i < fp.length; i++)
			{
				if(fp[i].type == "checkbox" && fp[i].checked)
					c++;
			}

			if(c > 0)
			{
				if(confirm("%%LNG_ConfirmDeleteCategories%%"))
					document.getElementById("frmCategories").submit();
			}
			else
			{
				alert("%%LNG_ChooseCategoryToDelete%%");
			}
		}

		var updatingSortables = false;
		var updateTimeout = null;
		function CreateSortableList() {
			$('#CategoryList').NestedSortable(
				{
					accept: 'SortableRow',
					noNestingClass: "no-nesting",
					opacity: .8,
					helperclass: 'SortableRowHelper',
					onChange: function(serialized) {
						updatingSortables = true;
						if(updateTimeout != null) window.clearTimeout(updateTimeout);
						$.ajax({
							url: 'remote.php?w=updateCategoryOrders',
							type: 'POST',
							dataType: 'xml',
							data: serialized[0].hash,
							success: function(response) {
								var status = $('status', response).text();
								var message = $('message', response).text();
								if(status == 0) {
									display_error('CategoriesStatus', message);
								}
								else {
									display_success('CategoriesStatus', message);
								}
								if(document.all) {
									// IE has problems here - it breaks on sortable lists so for now we just
									// refresh the current page
									window.location.reload();
								}
							}
						});

					},
					onStop: function() {
						if(document.all && updatingSortables == false) {
							// IE has problems here - it breaks on sortable lists so for now we just
							// refresh the current page
							updateTimeout = window.setTimeout(function() { window.location.reload(); }, 100);
						}
					},
					autoScroll: true,
					handle: '.sort-handle'
				}
			);
		}
		$(document).ready(function()
		{
			CreateSortableList();
		});
	</script>
