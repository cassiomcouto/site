<script type="text/javascript" src="script/categories.js?%%GLOBAL_JSCacheToken%%"></script>

			<script type="text/javascript">

				function ConfirmCancel()
				{
					if(confirm('%%GLOBAL_CancelMessage%%'))
					{
						document.location.href='index.php?ToDo=viewCategories';
					}
					else
					{
						return false;
					}
				}

				function CheckForm()
				{
					var catname = document.getElementById("catname");
					var cp = document.getElementById("catparentid");
					var cs = document.getElementById("catsort");
					var ci = document.getElementById("catimagefile");

					if(catname.value == "") {
						alert("%%LNG_NoCategoryName%%");
						catname.focus();
						catname.select();
						ShowTab('details');
						return false;
					}

					if(cp.selectedIndex == -1) {
						alert("%%LNG_NoParentCategory%%");
						cp.focus();
						return false;
					}

					if(isNaN(cs.value) || cs.value == "") {
						alert("%%LNG_NoCatSortOrder%%");
						cs.focus();
						cs.select();
						return false;
					}

					if(ci.value != "") {
						// Make sure it has a valid extension
						img = ci.value.split(".");
						ext = img[img.length-1].toLowerCase();

						if(ext != "jpg" && ext != "png" && ext != "gif") {
							alert("%%LNG_ChooseValidImage%%");
							ci.focus();
							ci.select();
							return false;
						}
					}

					//validate google optimzer form
					if ($('#catenableoptimizer').attr('checked'))
					{
						if(!Optimizer.ValidateConfigForm(ShowTab, 'optimizer')) {
							return false;
						}
					}

					// Everything is OK, return true
					return true;
				}

				function HandleRootCategory()
				{
					if ($('#catparentid').val() == 0) {
						document.getElementById('catimagefile').disabled = true;
						$('#HideImageUploadMessage').show();
						$('#OptionImageUploadMessage').hide();
					} else {
						document.getElementById('catimagefile').disabled = false;
						$('#HideImageUploadMessage').hide();
						$('#OptionImageUploadMessage').show();
					}
				}
				$(document).ready(function() {
					ShowTab('%%GLOBAL_CurrentTab%%');
				});

			</script>

			<form enctype="multipart/form-data" action="index.php?ToDo=%%GLOBAL_FormAction%%" onsubmit="return ValidateForm(CheckForm)" name="frmAddCategory" id="frmAddCategory" method="post">
			%%GLOBAL_hiddenFields%%
			<div class="BodyContainer">
			<table class="OuterPanel">
			  <tr>
				<td class="Heading1">%%GLOBAL_CatTitle%%</td>
				</tr>
				<tr>
				<td class="Intro">
					<p>%%GLOBAL_CatIntro%%</p>
					%%GLOBAL_Message%%
				</td>
			  </tr>
			  <tr>
			    <td>
					<div>
						<input type="submit" value="%%LNG_SaveAndExit%%" class="FormButton" />
						<input type="submit" value="%%GLOBAL_SaveAndAddAnother%%" name="AddAnother" class="FormButton" style="width:130px" />						<input type="button" name="CancelButton1" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()">
						<input id="currentTab" name="currentTab" value="details" type="hidden">

						<br /><img src="images/blank.gif" width="1" height="10" />
					</div>
				</td>
			  </tr>
			  	<tr>
					<td>
						<ul id="tabnav">
							<li><a href="#" class="active" id="tab_details" onclick="ShowTab('details')">%%LNG_Details%%</a></li>
							<li><a href="#" id="tab_optimizer" onclick="ShowTab('optimizer')">%%LNG_GoogleWebsiteOptimizer%%</a></li>
						</ul>
					</td>
				</tr>

				<tr>
					<td>
					<div id="div_details" style="padding-top: 10px;">
					  <table class="Panel">
						<tr>
						  <td class="Heading2" colspan=2>%%LNG_CatDetails%%</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<span class="Required">*</span>&nbsp;%%LNG_CatName%%:
							</td>
							<td>
								<input type="text" name="catname" id="catname" class="Field750" value="%%GLOBAL_CategoryName%%">
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_CatDesc%%:
							</td>
							<td>
								%%GLOBAL_WYSIWYG%%
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<span class="Required">*</span>&nbsp;%%LNG_CatParentCategory%%:
							</td>
							<td>
								<select size="5" name="catparentid" id="catparentid" class="Field750" style="height:115" onchange="HandleRootCategory()">
								%%GLOBAL_CategoryOptions%%
								</select>
								<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '%%LNG_CatParentCategory%%', '%%LNG_CatParentCategoryHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="d1"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_TemplateLayoutFile%%:
							</td>
							<td>
								<select name="catlayoutfile" id="catlayoutfile" class="Field750">
									%%GLOBAL_LayoutFiles%%
								</select>
								<img onmouseout="HideHelp('templatelayout');" onmouseover="ShowHelp('templatelayout', '%%LNG_TemplateLayoutFile%%', '%%LNG_CategoryTemplateLayoutFileHelp1%%%%GLOBAL_template%%%%LNG_CategoryTemplateLayoutFileHelp2%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="templatelayout"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<span class="Required">*</span>&nbsp;%%LNG_CatSort%%:
							</td>
							<td>
								<input type="text" name="catsort" id="catsort" class="Field" size="5" value="%%GLOBAL_CategorySort%%">
								<img onmouseout="HideHelp('d2');" onmouseover="ShowHelp('d2', '%%LNG_CatSort%%', '%%LNG_CatSortHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="d2"></div>
							</td>
						</tr>
					</table>
					<table width="100%" class="Panel">
						<tr>
						  <td class="Heading2" colspan=2>%%LNG_CatImage%%</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_CatImage%%:
							</td>
							<td>
								<input type="file" id="catimagefile" name="catimagefile" class="Field" %%GLOBAL_DisableFileUpload%% />
								<img onmouseout="HideHelp('d3');" onmouseover="ShowHelp('d3', '%%LNG_CatImage%%', '%%LNG_CatImageHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="d3"></div>
								<span id="HideImageUploadMessage" style="display: %%GLOBAL_ShowFileUploadMessage%%;">%%LNG_CatHideImageUploadMessage%%</span>
								<span id="OptionImageUploadMessage">%%GLOBAL_CatImageMessage%%</span>
							</td>
						</tr>
					</table>
					<table width="100%" class="Panel">
						<tr>
						  <td class="Heading2" colspan=2>%%LNG_SearchEngineOptimization%%</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_PageTitle%%:
							</td>
							<td>
								<input type="text" id="catpagetitle" name="catpagetitle" class="Field750" value="%%GLOBAL_CategoryPageTitle%%" />
								<img onmouseout="HideHelp('pagetitlehelp');" onmouseover="ShowHelp('pagetitlehelp', '%%LNG_PageTitle%%', '%%LNG_CategoryPageTitleHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="pagetitlehelp"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_MetaKeywords%%:
							</td>
							<td>
								<input type="text" id="catmetakeywords" name="catmetakeywords" class="Field750" value="%%GLOBAL_CategoryMetaKeywords%%" />
								<img onmouseout="HideHelp('metataghelp');" onmouseover="ShowHelp('metataghelp', '%%LNG_MetaKeywords%%', '%%LNG_MetaKeywordsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="metataghelp"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_MetaDescription%%:
							</td>
							<td>
								<input type="text" id="catmetadesc" name="catmetadesc" class="Field750" value="%%GLOBAL_CategoryMetaDesc%%" />
								<img onmouseout="HideHelp('metadeschelp');" onmouseover="ShowHelp('metadeschelp', '%%LNG_MetaDescription%%', '%%LNG_MetaDescriptionHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="metadeschelp"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_SearchKeywords%%:
							</td>
							<td>
								<input type="text" id="catsearchkeywords" name="catsearchkeywords" class="Field750" value="%%GLOBAL_CategorySearchKeywords%%">
								<img onmouseout="HideHelp('searchkeywords');" onmouseover="ShowHelp('searchkeywords', '%%LNG_SearchKeywords%%', '%%LNG_SearchKeywordsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="searchkeywords"></div>
							</td>
						</tr>
					 </table>
					 </div>
					 <div id="div_optimizer" style="padding-top: 10px; display:none;">
						<p class="InfoTip">%%GLOBAL_GoogleWebsiteOptimizerIntro%%</p>

						<table width="100%" class="Panel" style="%%GLOBAL_ShowEnableGoogleWebsiteOptimzer%%">
							<tr>
								<td class="Heading2" colspan="2">%%LNG_GoogleWebsiteOptimizer%%</td>
							</tr>
							<tr>
								<td class="FieldLabel">
									%%LNG_EnableGoogleWebsiteOptimizer%%?
								</td>
								<td>
									<input %%GLOBAL_DisableOptimizerCheckbox%% type="checkbox" name="catenableoptimizer" id="catenableoptimizer" %%GLOBAL_CheckEnableOptimizer%% onclick = "ToggleOptimizerConfigForm(%%GLOBAL_SkipOpimizerConfirmMsg%%);" />
									<label for="catenableoptimizer">%%LNG_YesEnableGoogleWebsiteOptimizer%%</label>
								</td>
							</tr>
						</table>
						%%GLOBAL_OptimizerConfigForm%%
					</div>
					<table width="100%" cellspacing="0" cellpadding="2" border="0" id="SaveButtons" class="PanelPlain">
						<tr>
							<td colspan="2">
								<input type="submit" value="%%LNG_SaveAndExit%%" class="FormButton" />
								<input type="submit" value="%%GLOBAL_SaveAndAddAnother%%" name="AddAnother" class="FormButton" style="width:130px" />
								<input type="button" name="CancelButton2" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()">
							</td>
						</tr>
						<tr><td class="Gap"></td></tr>
					 </table>
				</td>
			</tr>
		</table>
	</div>
</form>

