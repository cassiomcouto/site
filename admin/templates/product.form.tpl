<script type="text/javascript" src="../javascript/jquery/plugins/jquery.htmlEncode.js?%%GLOBAL_JSCacheToken%%"></script>

<input type="hidden" name="youTubeVideos" id="youTubeVideos" value="%%GLOBAL_YouTubeVideos%%">

<form enctype="multipart/form-data" action="index.php?ToDo=%%GLOBAL_FormAction%%" onsubmit="return ValidateForm(CheckAddProductForm)" id="frmProduct" method="post">
<input type="hidden" name="productId" id="productId" value="%%GLOBAL_ProductId%%">
<input type="hidden" name="productHash" id="productHash" value="%%GLOBAL_ProductHash%%">
<input type="hidden" name="originalProductId" id="originalProductId" value="%%GLOBAL_OriginalProductId%%">
<input id="currentTab" name="currentTab" value="0" type="hidden">
<input id="productVariationExisting" name="productVariationExisting" value="%%GLOBAL_ProductVariationExisting%%" type="hidden">
<div id="youTubeData" style="display: none;"></div>
<div class="BodyContainer">
	<table cellSpacing="0" cellPadding="0" width="100%" style="margin-left: 4px; margin-top: 8px;">
	<tr>
		<td class="Heading1">%%GLOBAL_Title%%</td>
	</tr>
	<tr>
		<td class="Intro">
			<p>%%GLOBAL_Intro%%</p>
			<div id="MainMessage">
				%%GLOBAL_Message%%
			</div>
			<p>
				<input type="submit" disabled="disabled" value="%%LNG_SaveAndExit%%" class="FormButton" />
				<input type="submit" disabled="disabled" value="%%GLOBAL_SaveAndAddAnother%%" onclick="SaveAndAddAnother()" class="FormButton" style="width:130px" />
				<input type="reset" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()" />
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<ul id="tabnav">
				<li><a href="#" class="active" id="tab0" onclick="ShowTab(0)">%%LNG_Details%%</a></li>
				<li style="display: %%GLOBAL_HideDigitalOptions%%"><a href="#" id="tab2" class="ShowOnDigitalProduct" onclick="ShowTab(2)" style="display: none;">%%LNG_DigitalDownloads%%</a></li>
				<li><a href="#" id="tab8" onclick="ShowTab(8)">%%LNG_ImagesVideos%%</a></li>
				<li><a href="#" id="tab1" onclick="ShowTab(1)">%%LNG_EventDate%%</a></li>
				<li style="display: %%GLOBAL_HideInventoryOptions%%"><a href="#" id="tab3" class="HideOnDigitalProduct" onclick="ShowTab(3)">%%LNG_Inventory%%</a></li>
				<li><a href="#" id="tab4" onclick="ShowTab(4)" class="HideOnDigitalProduct">%%LNG_Variations%%</a></li>
				<li><a href="#" id="tab5" onclick="ShowTab(5)">%%LNG_ConfigurableCustomFields%%</a></li>
				<li><a href="#" id="tab6" onclick="ShowTab(6)">%%LNG_OtherDetails%%</a></li>
				<li><a href="#" id="tab7" onclick="ShowTab(7)">%%LNG_DiscountRules%%</a></li>
				<li><a href="#" id="tab9" onclick="ShowTab(9)">%%LNG_GoogleWebsiteOptimizer%%</a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td>
			<!-- Start product details -->
			<div id="div0" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_CatalogInformationIntro%%</div>
				<table width="100%" class="Panel">
					<tr>
						<td class="Heading2" colspan="2">%%LNG_CatalogInformation%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_ProductType%%:
						</td>
						<td>
							<div id="ProductType" class="Field" style="float:left">
								<div>
									<input %%GLOBAL_ProdType_1%% id="ProductType_0" type="radio" name="prodtype" value="1" onclick="ToggleType(0)"/><label for="ProductType_0">%%LNG_PhysicalProduct%%</label>
								</div>
								<div>
									<input %%GLOBAL_ProdType_2%% id="ProductType_1" type="radio" name="prodtype" value="2" onclick="ToggleType(1)" /><label for="ProductType_1">%%LNG_DownloadableProduct%%</label>
								</div>
							</div>
							<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '%%LNG_ProductType%%', '%%LNG_ProductTypeHelp%%')" src="images/help.gif" width="24" height="16" border="0" style="margin-top: 5px;" />
							<div style="display:none" id="d1"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_ProductName%%:
						</td>
						<td>
							<input type="text" id="prodName" name="prodName" class="Field400" value="%%GLOBAL_ProdName%%">
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ProductCodeSKU%%:
						</td>
						<td>
							<input type="text" id="prodCode" name="prodCode" class="Field400" value="%%GLOBAL_ProdCode%%">
							<img onmouseout="HideHelp('d2');" onmouseover="ShowHelp('d2', '%%LNG_ProductCodeSKU%%', '%%LNG_ProductCodeSKUHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d2"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_Categories%%:<br />
							&nbsp;&nbsp;&nbsp;<span style="%%GLOBAL_HideCategoryCreation%%">(<a href="#" onclick="CreateNewCategory(); return false;">%%LNG_CreateNew%%</a>)</span>
						</td>
						<td>
							<select size="5" id="category" name="category[]" class="Field400 ISSelectReplacement" multiple="multiple" style="height: 140px;">
							%%GLOBAL_CategoryOptions%%
							</select>
							<img onmouseout="HideHelp('d3');" onmouseover="ShowHelp('d3', '%%LNG_Categories%%', '%%LNG_ProductCategoriesHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d3"></div>
						</td>
					</tr>
					<tr style="%%GLOBAL_HideVendorOption%%">
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_Vendor%%:
						</td>
						<td>
							<span style="%%GLOBAL_HideVendorLabel%%">%%GLOBAL_CurrentVendor%%</span>
							<select name="vendor" id="vendor" class="Field400" style="%%GLOBAL_HideVendorSelect%%" onchange="toggleVendorSettings($(this).val());">
								%%GLOBAL_VendorList%%
							</select>
							<img style="%%GLOBAL_HideVendorSelect%%" onmouseout="HideHelp('vendorhelp');" onmouseover="ShowHelp('vendorhelp', '%%LNG_Vendor%%', '%%LNG_ProductVendorHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="vendorhelp"></div>
						</td>
					</tr>
				</table>
				<table width="100%" class="Panel">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_ProductDescription%%</td>
					</tr>
					<tr>
						<td colspan="2">
							%%GLOBAL_WYSIWYG%%
						</td>
					</tr>
				</table>
				<table width="100%" class="Panel">
					<tr>
						<td class="Heading2" colspan="2">%%LNG_ProductPriceOptions%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_Price%%:
						</td>
						<td>
							%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="prodPrice" name="prodPrice" class="Field80" value="%%GLOBAL_ProdPrice%%" style="text-align: right;" /> %%GLOBAL_CurrencyTokenRight%% %%GLOBAL_PriceMsg%%
							<img onmouseout="HideHelp('d6');" onmouseover="ShowHelp('d6', '%%LNG_Price%%', '%%LNG_PriceHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d6"></div>
							<a href="javascript:void(0)" class="ExpandLink" onclick="toggle_price_options()"><span id="more_price_options">%%LNG_MorePricingOptions%% &raquo;</span></a>
						</td>
					</tr>
					<tr id="tr_costprice" style="display:none">
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_CostPrice%%:
						</td>
						<td>
							%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="prodCostPrice" name="prodCostPrice" class="Field80" value="%%GLOBAL_ProdCostPrice%%" style="text-align: right;" /> %%GLOBAL_CurrencyTokenRight%%
							<img onmouseout="HideHelp('d7');" onmouseover="ShowHelp('d7', '%%LNG_CostPrice%%', '%%LNG_CostPriceHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d7"></div>
						</td>
					</tr>
					<tr id="tr_retailprice" style="display:none">
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_RetailPrice%%:
						</td>
						<td>
							%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="prodRetailPrice" name="prodRetailPrice" class="Field80" value="%%GLOBAL_ProdRetailPrice%%" style="text-align: right;" /> %%GLOBAL_CurrencyTokenRight%%
							<img onmouseout="HideHelp('d8');" onmouseover="ShowHelp('d8', '%%LNG_RetailPrice%%', '%%LNG_RetailPriceHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d8"></div>
						</td>
					</tr>
					<tr id="tr_saleprice" style="display:none">
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_SalePrice%%:
						</td>
						<td>
							%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="prodSalePrice" name="prodSalePrice" class="Field80" value="%%GLOBAL_ProdSalePrice%%" style="text-align: right;" /> %%GLOBAL_CurrencyTokenRight%%
							<img onmouseout="HideHelp('d9');" onmouseover="ShowHelp('d9', '%%LNG_SalePrice%%', '%%LNG_SalePriceHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d9"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;
						</td>
						<td>
							<label><input type="checkbox" name="prodIsTaxable" value="1" %%GLOBAL_ProdIsTaxable%% /> %%LNG_YesThisProductIsTaxable%%</label>
							<img onmouseout="HideHelp('prodistaxablehelp');" onmouseover="ShowHelp('prodistaxablehelp', '%%LNG_ProductIsTaxable%%?', '%%LNG_ProductIsTaxableHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="prodistaxablehelp"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;
						</td>
						<td>
							<label><input type="checkbox" name="prodAllowPurchasing" id="prodAllowPurchasing" value="1" onclick="ToggleAllowPurchasing();" %%GLOBAL_ProdAllowPurchases%% /> %%LNG_ProductAllowPurchasing%%</label>
							<img onmouseout="HideHelp('prodallowpurchasinghelp');" onmouseover="ShowHelp('prodallowpurchasinghelp', '%%LNG_ProductAllowPurchasing%%?', '%%LNG_ProductAllowPurchasingHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="prodallowpurchasinghelp"></div>
						</td>
					</tr>
					<tr id="prodCallForPricingOptions">
						<td class="FieldLabel">
							&nbsp;
						</td>
						<td>
							<label><input type="checkbox" name="prodHidePrices" id="prodHidePrices" value="1" %%GLOBAL_ProdHidePrice%% onclick="ToggleCallForPricing()" /> %%LNG_ProductCallForPricing%%</label>
							<img onmouseout="HideHelp('prodhidepriceshelp');" onmouseover="ShowHelp('prodhidepriceshelp', '%%LNG_ProductCallForPricing%%?', '%%LNG_ProductCallForPricingHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="prodhidepriceshelp"></div>
							<div id="prodCallForPricingLabel" style="margin-left: 18px;" >
								<img src="images/nodejoin.gif" alt="" style="vertical-align: middle;" /> %%LNG_ProductCallForPricingLabel%%: <input type="text" name="prodCallForPricingLabel" class="Field250" value="%%GLOBAL_ProdCallForPricingLabel%%" />
							</div>
						</td>
					</tr>
				</table>
				<table width="100%" class="Panel HideOnDigitalProduct" id="shipping_table">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_ShippingDetails%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_ProductWeight%%:
						</td>
						<td>
							<input type="text" id="prodWeight" name="prodWeight" class="Field80" value="%%GLOBAL_ProdWeight%%" style="text-align: right;" /> %%GLOBAL_WeightMeasurement%%
							<img onmouseout="HideHelp('d14');" onmouseover="ShowHelp('d14', '%%LNG_ProductWeight%%', '%%GLOBAL_ProductWeightHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d14"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ProductWidth%%:
						</td>
						<td>
							<input type="text" id="prodWidth" name="prodWidth" class="Field80" value="%%GLOBAL_ProdWidth%%" style="text-align: right;" /> %%GLOBAL_LengthMeasurement%%
							<img onmouseout="HideHelp('d15');" onmouseover="ShowHelp('d15', '%%LNG_ProductWidth%%', '%%GLOBAL_ProductWidthHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d15"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ProductHeight%%:
						</td>
						<td>
							<input type="text" id="prodHeight" name="prodHeight" class="Field80" value="%%GLOBAL_ProdHeight%%" style="text-align: right;" /> %%GLOBAL_LengthMeasurement%%
							<img onmouseout="HideHelp('d16');" onmouseover="ShowHelp('d16', '%%LNG_ProductHeight%%', '%%GLOBAL_ProductHeightHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d16"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ProductDepth%%:
						</td>
						<td>
							<input type="text" id="prodDepth" name="prodDepth" class="Field80" value="%%GLOBAL_ProdDepth%%" style="text-align: right;" /> %%GLOBAL_LengthMeasurement%%
							<img onmouseout="HideHelp('d17');" onmouseover="ShowHelp('d17', '%%LNG_ProductDepth%%', '%%GLOBAL_ProductDepthHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d17"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_FixedShippingCost%%:
						</td>
						<td>
							%%GLOBAL_CurrencyTokenLeft%% <input type="text" id="prodFixedCost" name="prodFixedCost" class="Field80" style="width:70px; text-align: right;" value="%%GLOBAL_ProdFixedShippingCost%%" onkeyup="document.getElementById('prodFreeShipping').checked=false"> %%GLOBAL_CurrencyTokenRight%%
							<img onmouseout="HideHelp('d21');" onmouseover="ShowHelp('d21', '%%LNG_FixedShippingCost%%', '%%LNG_FixedShippingCostHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d21"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_FreeShipping%%:
						</td>
						<td>
							<input type="checkbox" id="prodFreeShipping" name="prodFreeShipping" value="1" %%GLOBAL_FreeShipping%% onclick="if(this.checked) { document.getElementById('prodFixedCost').value='0'; }"> <label for="prodFreeShipping">%%LNG_YesFreeShipping%%</label>
							<img onmouseout="HideHelp('d30');" onmouseover="ShowHelp('d30', '%%LNG_FreeShipping%%', '%%LNG_FreeShippingProductHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d30"></div>
						</td>
					</tr>
				</table>
			</div> <!-- End product details -->

			<!-- Start event/delivery date -->
			<div id="div1" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_EventDateIntro%%</div>
				  <table width="100%" class="Panel">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_EventDate%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">&nbsp;&nbsp;&nbsp;%%LNG_EventDateRequired%%</td>
						<td>
							<label><input id="EventDateRequired" name="EventDateRequired" type="checkbox" %%GLOBAL_EventDateRequired%% /> %%LNG_EventDateRequiredInfo%%</label>
							<img onmouseout="HideHelp('ed1');" onmouseover="ShowHelp('ed1', '%%LNG_EventDateRequiredHelpHeader%%', '%%LNG_EventDateRequiredHelp%%')" src="images/help.gif" width="24" height="16" border="0" style="margin-top: 5px;" />
							<div style="display:none" id="ed1"></div>
						</td>
					</tr>
					<tr id="DateFieldNameTR">
						<td class="FieldLabel">
							<span class="Required">*</span>
							%%LNG_EventDateFieldName%%
						</td>
						<td>
							<input id="EventDateFieldName" name="EventDateFieldName" value="%%GLOBAL_EventDateFieldName%%" type="text" class="Field300" />
							<img onmouseout="HideHelp('ed2');" onmouseover="ShowHelp('ed2', '%%LNG_EventDateFieldNameHelpHeader%%', '%%LNG_EventDateFieldNameHelp%%')" src="images/help.gif" width="24" height="16" border="0" style="margin-top: 5px;" />
							<div style="display:none" id="ed2"></div>
						</td>
					</tr>
					<tr id="DateLimitTR">
						<td class="FieldLabel">&nbsp;&nbsp;&nbsp;%%LNG_EventDateLimit%%
						</td>
						<td style="padding-bottom: 10px;">
							<label><input id="LimitDates" name="LimitDates" type="checkbox" %%GLOBAL_LimitDates%% /> %%LNG_EventDateLimitInfo%%</label>
							<select id="LimitDatesSelect" name="LimitDatesSelect">
								<option value="1" %%GLOBAL_LimitDateOption1%%>%%LNG_EventDateLimitOption1%%</option>
								<option value="2" %%GLOBAL_LimitDateOption2%%>%%LNG_EventDateLimitOption2%%</option>
								<option value="3" %%GLOBAL_LimitDateOption3%%>%%LNG_EventDateLimitOption3%%</option>
							</select>
							%%LNG_EventDateLimitInfo2%%
							<img onmouseout="HideHelp('ed3');" onmouseover="ShowHelp('ed3', '%%LNG_EventDateLimitHelpHeader%%', '%%LNG_EventDateLimitHelp%%')" src="images/help.gif" width="24" height="16" border="0" style="margin-top: 5px;" />
							<div style="display:none" id="ed3"></div>
							<br/>
							<div id="LimitDates1">
								<img style="float: left;" src="images/nodejoin.gif"/>
								<span id=customDate7 style="float : left; display:block; margin-top:2px;">&nbsp;
								%%LNG_EventDateLimitOption1Info%%
								<select name="Calendar1[From][Day]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewFromDays%%
								</select>
								<select name="Calendar1[From][Mth]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewFromMonths%%
								</select>
								<select name="Calendar1[From][Yr]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewFromYears%%
								</select>
								<span class=body>%%LNG_EventDateLimitOption1Info2%%</span>
								<select name="Calendar1[To][Day]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewToDays%%
								</select>
								<select name="Calendar1[To][Mth]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewToMonths%%
								</select>
								<select name="Calendar1[To][Yr]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewToYears%%
								</select>
								</span>&nbsp;
							</div>
							<div id="LimitDates2">
								<img style="float: left;" src="images/nodejoin.gif"/>
								<span id=customDate7 style="float : left; display:block; margin-top:2px;">&nbsp;
								%%LNG_EventDateLimitOption2Info%%
								<select name="Calendar2[From][Day]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewFromDays%%
								</select>
								<select name="Calendar2[From][Mth]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewFromMonths%%
								</select>
								<select name="Calendar2[From][Yr]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewFromYears%%
								</select>
								</span>&nbsp;
							</div>
							<div id="LimitDates3">
								<img style="float: left;" src="images/nodejoin.gif"/>
								<span id=customDate7 style="float : left; display:block; margin-top:2px;">&nbsp;
								%%LNG_EventDateLimitOption3Info%%
								<select name="Calendar3[To][Day]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewToDays%%
								</select>
								<select name="Calendar3[To][Mth]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewToMonths%%
								</select>
								<select name="Calendar3[To][Yr]" class="" style="margin-bottom:3px">
									%%GLOBAL_OverviewToYears%%
								</select>
								</span>&nbsp;
							</div>
						</td>
					</tr>
				 </table>
			</div> <!-- End event/delivery date -->

			<!-- Start download file -->
			<div id="div2" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_ProductDownloadIntro%%</div>
				<div id="DownloadStatus"></div>
				  <table class="Panel">
					<tr id="ExistingDownloads" style="display: %%GLOBAL_DisplayDownloaadGrid%%;">
						<td style="padding-top: 5px;" colspan="2" >
							<table class="GridPanel" cellspacing="0" cellpadding="0" border="0" id="ExistingDownloadsGrid" style="width:100%;">
								<thead>
									<tr class="Heading3">
										<td align="center">&nbsp;</td>
										<td>%%LNG_FileName%%</td>
										<td align="right" nowrap="nowrap">%%LNG_FileSize%%</td>
										<td align="right"><span onmouseover="ShowQuickHelp(this, '%%LNG_Downloads%%', '%%LNG_DownloadsHelp%%');" onmouseout="HideQuickHelp(this);" class="HelpText">%%LNG_Downloads%%</span></td>
										<td nowrap="nowrap">%%LNG_MaximumDownloads%%</td>
										<td nowrap="nowrap">%%LNG_ExpiresAfterHeader%%</td>
										<td>%%LNG_Action%%</td>
									</tr>
								</thead>
								<tbody>
								%%GLOBAL_DownloadsGrid%%
								</tbody>
							</table>
						</td>
					</tr>
					<tr id="DownloadUploadGap" style="display: %%GLOBAL_DisplayDownloadUploadGap%%">
						<td class="Sep" colspan="2">&nbsp;</td>
					</tr>
					<tr style="display: %%GLOBAL_DisplayDownloadUploadHeading%%">
						<td class="Heading2" id="DownloadUploadHeading" colspan="2">%%LNG_DigitalDownloadUploadHeading%%</td>
					</tr>
					<tbody id="NewDownload" style="display: %%GLOBAL_DisplayNewDownload%%">
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_DownloadDescription%%:
							</td>
							<td>
								<input type="text" name="downdescription" id="DownloadDescription" class="Field200" />
							</td>
						</tr>
						<tr id="EditDownload" style="display: none;">
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_EditExistingDownload%%:
							</td>
							<td id="EditDownloadFile">
							&nbsp;
							</td>
						</tr>
						<tr id="NewDownloadUpload">
							<td class="FieldLabel">
								<span class="Required">*</span>&nbsp;%%LNG_UploadNewDownload%%:
							</td>
							<td>
								<input type="hidden" name="downloadid" id="CurrentDownloadId" value="" />
								<div id="StatusUploading" style="display: none;">
									<input type="button" class="SmallButton" disabled="disabled" value="%%LNG_SavingDownload%%" />
								</div>
								<label><input id="ProductImportUseUpload" type="radio" name="useserver" value="0" checked="checked" onclick="ToggleSource();" /> %%LNG_ImportFileUpload%% %%LNG_MaxUploadSize%%</label>
								<div id="StatusNormal">
									<input type="file" class="Field" name="newdownload" id="NewDownloadFile" />
									<span id="EditDownloadButtons" style="display: none;">
										<input type="button" value="%%LNG_SaveDownload%%" onclick="saveDownload();" class="SmallButton" style="width: 90px;" />
										<input type="button" value="%%LNG_CancelEdit%%" onclick="cancelDownloadEdit();" class="SmallButton" style="width: 60px" />
									</span>
								</div>
								<span style="font-size: 11px; font-style: italic;">%%LNG_MaxUploadSize%%</span>
								<div>
									<label><input id="ProductImportUseServer" type="radio" name="useserver" value="1" onclick="ToggleSource();" /> %%LNG_ImportProductFileServer%%</label>
									<img onmouseout="HideHelp('i1');" onmouseover="ShowHelp('i1', '%%LNG_ImportProductFileServer%%', '%%LNG_ImportProductFileServerDesc%%')" src="images/help.gif" width="24" height="16" border="0">
									<div style="display: none;" id="i1"></div>
								</div>
								<div id="ProductImportServerField" style="margin-left: 25px; display: none;">
									<select name="serverfile" id="ServerFile" class="Field250">
										<option value="">%%LNG_ImportChooseFile%%</option>
										%%GLOBAL_ServerFiles%%
									</select>
								</div>
								<div id="ProductImportServerNoList" style="margin: 5px 0 0 25px; display: none; font-style: italic;" class="Field500">
									%%LNG_FieldNoServerFilesProductDownloads%%
								</div>
								<br/>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_ExpiresAfter%%:
							</td>
							<td>
								<input type="text" name="downexpiresafter" id="DownloadExpiresAfter" class="Field40" />
								<select name="downloadexpiresrange" id="DownloadExpiresRange">
									<option value="days">%%LNG_RangeDays%%</option>
									<option value="weeks">%%LNG_RangeWeeks%%</option>
									<option value="months">%%LNG_RangeMonths%%</option>
									<option value="years">%%LNG_RangeYears%%</option>
								</select>
								<img onmouseout="HideHelp('dlexpires');" onmouseover="ShowHelp('dlexpires', '%%LNG_ExpiresAfter%%', '%%LNG_ExpiresAfterHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="dlexpires"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								&nbsp;&nbsp;&nbsp;%%LNG_MaximumDownloads%%:
							</td>
							<td>
								<input type="text" name="downmaxdownloads" id="DownloadMaxDownloads" class="Field40" />
								<img onmouseout="HideHelp('dldownloads');" onmouseover="ShowHelp('dldownloads', '%%LNG_MaximumDownloads%%', '%%LNG_MaximumDownloadsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="dldownloads"></div>
							</td>
						</tr>
						<tr>
							<td clas="FieldLabel">&nbsp;</td>
							<td>
								<input type="button" value="%%LNG_AttachFile%%" onclick="attachFile();" class="FormButton Field120" />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>

							</td>
						</tr>
					</tbody>
				  </table>
			</div> <!-- End download file -->

			<!-- Start inventory tracking -->
			<div id="div3" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_InventoryTrackingIntro%%</div>
				  <table class="Panel" id="tabInventory">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_InventoryTracking%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_TrackingMethod%%:
						</td>
						<td>
							<input type="radio" id="prodInvTrack_0" name="prodInvTrack" value="0" onclick="ToggleProductInventoryOptions(false); toggleVariationInventoryColumns();" %%GLOBAL_InvTrack_0%%> <label for="prodInvTrack_0">%%LNG_DoNotTrackInventory%%</label>
							<img onmouseout="HideHelp('d23');" onmouseover="ShowHelp('d23', '%%LNG_TrackingMethod%%', '%%LNG_TrackingMethodHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d23"></div><br />
							<input type="radio" id="prodInvTrack_1" name="prodInvTrack" value="1" onclick="ToggleProductInventoryOptions(true); toggleVariationInventoryColumns();" %%GLOBAL_InvTrack_1%%> <label for="prodInvTrack_1">%%LNG_TrackInvForProduct%%</label><br />
							<div id="divTrackProd" style="display: %%GLOBAL_HideProductInventoryOptions%%; padding-left:30pt; padding-top:3pt; padding-bottom:3pt">
								<table border="0">
									<tr>
										<td>
											%%LNG_CurrentStockLevel%%:
										</td>
										<td>
											<input type="text" id="prodCurrentInv" name="prodCurrentInv" class="Field50" value="%%GLOBAL_CurrentStockLevel%%">
											<img onmouseout="HideHelp('d24');" onmouseover="ShowHelp('d24', '%%LNG_CurrentStockLevel%%', '%%LNG_CurrentStockLevelHelp%%')" src="images/help.gif" width="24" height="16" border="0">
											<div style="display:none" id="d24"></div>
										</td>
									</tr>
									<tr>
										<td>
											%%LNG_LowStockLevel1%%:
										</td>
										<td>
											<input type="text" id="prodLowInv" name="prodLowInv" class="Field50" value="%%GLOBAL_LowStockLevel%%">
											<img onmouseout="HideHelp('d25');" onmouseover="ShowHelp('d25', '%%LNG_LowStockLevel%%', '%%LNG_LowStockLevelHelp%%')" src="images/help.gif" width="24" height="16" border="0">
											<div style="display:none" id="d25"></div>
										</td>
									</tr>
									<tr>
								</table>
							</div>
							<input type="radio" id="prodInvTrack_2" name="prodInvTrack" value="2" onclick="ToggleProductInventoryOptions(false); toggleVariationInventoryColumns();" %%GLOBAL_InvTrack_2%%> <label for="prodInvTrack_2">%%LNG_TrackInvForProductOpt%%</label>
						</td>
					</tr>
				 </table>
			</div><!-- End inventory tracking -->

			<!-- Start product variations -->
			<div id="div4" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_ProductVariationsIntro%%</div>
				  <table class="Panel" id="tabInventory">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_ProductVariationOptions%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ThisProduct%%:
						</td>
						<td style="padding-bottom:10px">
							<input type="radio" name="useProdVariation" id="useProdVariationNo" value="0" %%GLOBAL_IsNoVariation%% /> <label for="useProdVariationNo">%%LNG_ProductWillNotUseVariation%%</label><br />
							<input type="radio" name="useProdVariation" id="useProdVariationYes" value="1" %%GLOBAL_IsYesVariation%% %%GLOBAL_VariationDisabled%% /> <label for="useProdVariationYes" id="variationLabel" style="color:%%GLOBAL_VariationColor%%">%%LNG_ProductWillUseVariation%%</label>
							<span style="display:%%GLOBAL_HideVariationList%%;" id="variationList">
								<select class="Field200" name="variationId" id="variationId">
									<option value="">%%LNG_ChooseAVariation%%</option>
									%%GLOBAL_VariationOptions%%
								</select>
								<div style="padding-left:20px">
									<input type="checkbox" name="prodOptionsRequired" id="prodOptionsRequired" %%GLOBAL_OptionsRequired%% value="ON" /> <label for="prodOptionsRequired">%%LNG_ProductOptionRequired%%</label>
									<img onmouseout="HideHelp('dforceopt');" onmouseover="ShowHelp('dforceopt', '%%LNG_ProductOptionRequiredTitle%%', '%%LNG_ProductOptionRequiredHelp%%')" src="images/help.gif" width="24" height="16" border="0">
									<div style="display:none" id="dforceopt"></div>
								</div>
							</span>
						</td>
					</tr>
				</table>
				<div style="display:%%GLOBAL_HideVariationCombinationList%%; margin-bottom:5px" class="GridContainer" id="variationCombinationsList">
					%%GLOBAL_VariationCombinationList%%
				</div>
			</div><!-- End product variations -->

			<!-- Start custom fields -->
			<div id="div5" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_CustomFieldsIntro%%</div>
				  <table class="Panel">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_CustomFields%%</td>
					</tr>
				  </table>
				  <table class="Panel" id="CustomFieldsContainer">
				  <tbody>
					%%GLOBAL_CustomFields%%
				  </tbody>
				  </table>
				  <div style="padding:10px 0px 10px 5px">%%LNG_ConfigurableFieldsIntro%%</div>
					<table width="100%" class="Panel">
						<tr>
							<td class="Heading2" colspan="4">%%LNG_ConfigurableFields%%</td>
						</tr>
					</table>
					<input type="hidden" id="FieldLastKey" value="%%GLOBAL_FieldLastKey%%" />
					<table width="100%" class="Panel" id="ProductFieldsContainer">
						%%GLOBAL_ProductFields%%
					</table>
			</div><!-- End custom fields -->

			<!-- Start related products -->
			<div id="div6" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_RelatedProductsIntro%%</div>
				  <table width="100%" class="Panel">
					<tr>
					  <td class="Heading2" colspan=2>%%LNG_RelatedProducts%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_RelatedProducts%%:
						</td>
						<td>
							<input type="checkbox" id="prodRelatedAuto" name="prodRelatedAuto" value="1" onclick="toggle_related_auto(this.checked)" %%GLOBAL_IsProdRelatedAuto%%> <label for="prodRelatedAuto">%%LNG_ProductRelatedOptionsAutomatically%%</label>
							<img onmouseout="HideHelp('d34');" onmouseover="ShowHelp('d34', '%%LNG_RelatedProducts%%', '%%LNG_RelatedProductsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d34"></div>
							<blockquote id="relatedProductsBoxes">
								<select id="relCategory" size="10" name="relCategory" class="Field400" onchange="GetProducts(this)">
									%%GLOBAL_RelatedCategoryOptions%%
								</select>
								<br />
								<select size="10" id="relProducts" name="relProducts" onDblClick="AddRelatedProduct(this)" class="Field400">
								</select>
								<br />
								* <em>%%LNG_DoubleClickToAdd%%</em><br />
								<select multiple size="5" id="related" name="prodRelatedProducts[]" onDblClick="RemoveRelatedProduct(this)" class="Field400">%%GLOBAL_RelatedProductOptions%%</select><br />
								* <em>%%LNG_DoubleClickToRemove%%</em><br />
							</blockquote>
						</td>
					</tr>
				 </table>
				 <br />
				<div style="padding-bottom:5px">%%LNG_OtherDetailsIntro%%</div>
				<table width="100%" class="Panel">
					<tr>
						<td class="Heading2" colspan="2">%%LNG_OtherDetails%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ProductWarranty%%:
						</td>
						<td>
							<textarea id="prodWarranty" name="prodWarranty" class="Field400" rows="5">%%GLOBAL_ProdWarranty%%</textarea>
							<img onmouseout="HideHelp('d18');" onmouseover="ShowHelp('d18', '%%LNG_ProductWarranty%%', '%%LNG_ProductWarrantyHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d18"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_TemplateLayoutFile%%:
						</td>
						<td>
							<select name="prodlayoutfile" id="prodlayoutfile" class="Field400">
								%%GLOBAL_LayoutFiles%%
							</select>
							<img onmouseout="HideHelp('templatelayout');" onmouseover="ShowHelp('templatelayout', '%%LNG_TemplateLayoutFile%%', '%%LNG_ProductTemplateLayoutFileHelp1%%%%GLOBAL_template%%%%LNG_ProductTemplateLayoutFileHelp2%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="templatelayout"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_BrandName%%:
						</td>
						<td>
							<select name="brandbox" id="brandbox" class="Field200">
								<option value="">%%LNG_ChooseAnExistingBrand%%</option>
								%%GLOBAL_BrandNameOptions%%
							</select>
							<span style="%%GLOBAL_HideAddBrandBox%%">
								%%LNG_OrCreateANewBrand%%:
								<input type="text" id="brandname" name="brandname" class="Field100" style="width:120px" value="%%GLOBAL_BrandName%%">
							</span>
							<img onmouseout="HideHelp('d33');" onmouseover="ShowHelp('d33', '%%LNG_BrandName%%', '%%GLOBAL_BrandNameProdHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d33"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_SearchKeywords%%:
						</td>
						<td>
							<input type="text" id="prodSearchKeywords" name="prodSearchKeywords" class="Field400" value="%%GLOBAL_ProdSearchKeywords%%">
							<img onmouseout="HideHelp('d19');" onmouseover="ShowHelp('d19', '%%LNG_SearchKeywords%%', '%%LNG_SearchKeywordsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d19"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_ProductTags%%:
						</td>
						<td>
							<input type="text" id="prodTags" name="prodTags" class="Field400" value="%%GLOBAL_ProdTags%%">
							<img onmouseout="HideHelp('d19');" onmouseover="ShowHelp('d19', '%%LNG_ProductTags%%', '%%LNG_ProductTagsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d19"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_Availability%%:
						</td>
						<td>
							<input type="text" id="prodAvailability" name="prodAvailability" class="Field400" value="%%GLOBAL_ProdAvailability%%">
							<img onmouseout="HideHelp('d27');" onmouseover="ShowHelp('d27', '%%LNG_Availability%%', '%%LNG_AvailabilityHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d27"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_Visible%%:
						</td>
						<td>
							<input type="checkbox" id="prodVisible" name="prodVisible" value="1" %%GLOBAL_ProdVisible%%> <label for="prodVisible">%%LNG_YesProductVisible%%</label>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_FeaturedProduct%%:
						</td>
						<td>
							<div style="%%GLOBAL_HideStoreFeatured%%">
								<input type="checkbox" id="prodFeatured" name="prodFeatured" value="1" %%GLOBAL_ProdFeatured%%> <label for="prodFeatured">%%LNG_YesProductFeatured%%</label>
								<img onmouseout="HideHelp('d11');" onmouseover="ShowHelp('d11', '%%LNG_FeaturedProduct%%', '%%LNG_FeaturedProductHelp%%')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="d11"></div>
							</div>
							<div style="%%GLOBAL_HideVendorFeatured%%" id="vendorFeaturedToggle">
								<input type="checkbox" id="prodvendorfeatured" name="prodvendorfeatured" value="1" %%GLOBAL_ProdVendorFeatured%%> <label for="prodvendorfeatured">%%LNG_YesProductVendorFeatured%%</label>
								<img onmouseout="HideHelp('prodvendorfeaturedhelp');" onmouseover="ShowHelp('prodvendorfeaturedhelp', '%%LNG_VendorFeaturedProduct%%', '%%LNG_VendorFeaturedProductHelp%%')" src="images/help.gif" width="24" height="16" border="0" />
								<div style="display:none" id="prodvendorfeaturedhelp"></div>
							</div>
						</td>
					</tr>

					<tr class="HideOnDigitalProduct">
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_GiftWrapping%%:
						</td>
						<td>
							<label><input type="radio" onclick="ToggleGiftWrapping(this.value)" name="prodwraptype" value="default" %%GLOBAL_WrappingOptionsDefaultChecked%% /> %%LNG_ProductGiftWrappingDefault%%</label>
							<img onmouseout="HideHelp('prodwrappinghelp');" onmouseover="ShowHelp('prodwrappinghelp', '%%LNG_GiftWrapping%%', '%%LNG_GiftWrappingHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="prodwrappinghelp"></div><br />
							<label style="display: black;"><input type="radio" onclick="ToggleGiftWrapping(this.value)" name="prodwraptype" value="none" %%GLOBAL_WrappingOptionsNoneChecked%% /> %%LNG_ProductGiftWrappingNone%%</label>
							<label style="display: block;"><input type="radio" onclick="ToggleGiftWrapping(this.value)" name="prodwraptype" id="prodwraptype_custom" value="custom" %%GLOBAL_WrappingOptionsCustomChecked%% /> %%LNG_ProductGiftWrappingCustom%%</label>
							<div style="%%GLOBAL_HideGiftWrappingOptions%%" id="GiftWrapOptions">
								<img src="images/nodejoin.gif" alt="" style="float: left; margin-right: 10px;" />
								<select name="prodwrapoptions[]" id="prodwrapoptions" multiple="multiple" size="10" class="Field300 ISSelectReplacement">
									%%GLOBAL_WrappingOptions%%
								</select>
							</div>
						</td>
					</tr>

					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_SortOrder%%:
						</td>
						<td>
							<input type="text" id="prodSortOrder" name="prodSortOrder" class="Field80" style="width:30px" value="%%GLOBAL_ProdSortOrder%%">
							<img onmouseout="HideHelp('d20');" onmouseover="ShowHelp('d20', '%%LNG_SortOrder%%', '%%LNG_SortOrderHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d20"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							<span class="Required">*</span>&nbsp;%%LNG_ProductCondition%%:
						</td>
						<td>
							<select name="prodCondition" id="prodCondition">
								<option value="New" %%GLOBAL_ProdConditionNewSelected%%>%%LNG_ConditionNew%%</option>
								<option value="Used" %%GLOBAL_ProdConditionUsedSelected%%>%%LNG_ConditionUsed%%</option>
								<option value="Refurbished" %%GLOBAL_ProdConditionRefurbishedSelected%%>%%LNG_ConditionRefurbished%%</option>
							</select>
							<img onmouseout="HideHelp('dcond');" onmouseover="ShowHelp('dcond', '%%LNG_ProductCondition%%', '%%LNG_ProductConditionHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="dcond"></div>
							<label><input type="checkbox" name="prodShowCondition" id="prodShowCondition" value="1" %%GLOBAL_ProdShowCondition%% />%%LNG_ShowConditionOnProduct%%</label>
						</td>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td class="Heading2" colspan="2">%%LNG_SearchEngineOptimization%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_PageTitle%%:
						</td>
						<td>
							<input type="text" id="prodPageTitle" name="prodPageTitle" class="Field400" value="%%GLOBAL_ProdPageTitle%%" />
							<img onmouseout="HideHelp('pagetitlehelp');" onmouseover="ShowHelp('pagetitlehelp', '%%LNG_PageTitle%%', '%%LNG_ProdPageTitleHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="pagetitlehelp"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_MetaKeywords%%:
						</td>
						<td>
							<input type="text" id="prodMetaKeywords" name="prodMetaKeywords" class="Field400" value="%%GLOBAL_ProdMetaKeywords%%" />
							<img onmouseout="HideHelp('metataghelp');" onmouseover="ShowHelp('metataghelp', '%%LNG_MetaKeywords%%', '%%LNG_MetaKeywordsHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="metataghelp"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_MetaDescription%%:
						</td>
						<td>
							<input type="text" id="prodMetaDesc" name="prodMetaDesc" class="Field400" value="%%GLOBAL_ProdMetaDesc%%" />
							<img onmouseout="HideHelp('metadeschelp');" onmouseover="ShowHelp('metadeschelp', '%%LNG_MetaDescription%%', '%%LNG_MetaDescriptionHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="metadeschelp"></div>
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td class="Heading2" colspan="2">%%LNG_MYOBSettings%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_MYOBAsset%%:
						</td>
						<td>
							<input type="text" class="Field" id="prodMYOBAsset" name="prodMYOBAsset" maxlength="6" size="6" value="%%GLOBAL_ProdMYOBAsset%%" />
							<img onmouseout="HideHelp('myobassethelp');" onmouseover="ShowHelp('myobassethelp', '%%LNG_MYOBAsset%%', '%%LNG_MYOBAssetHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="myobassethelp"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_MYOBIncome%%:
						</td>
						<td>
							<input type="text" class="Field" id="prodMYOBIncome" name="prodMYOBIncome" maxlength="6" size="6" value="%%GLOBAL_ProdMYOBIncome%%" />
							<img onmouseout="HideHelp('myobincomehelp');" onmouseover="ShowHelp('myobincomehelp', '%%LNG_MYOBIncome%%', '%%LNG_MYOBIncomeHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="myobincomehelp"></div>
						</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_MYOBExpense%%:
						</td>
						<td>
							<input type="text" class="Field" id="prodMYOBExpense" name="prodMYOBExpense" maxlength="6" size="6" value="%%GLOBAL_ProdMYOBExpense%%" />
							<img onmouseout="HideHelp('myobexpensehelp');" onmouseover="ShowHelp('myobexpensehelp', '%%LNG_MYOBExpense%%', '%%LNG_MYOBExpenseHelp%%')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="myobexpensehelp"></div>
						</td>
					</tr>
					<tr>
						<td class="Heading2" colspan="2">%%LNG_PeachtreeSettings%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							&nbsp;&nbsp;&nbsp;%%LNG_PeachtreeGL%%:
						</td>
						<td>
							<input type="text" class="Field" id="prodPeachtreeGL" name="prodPeachtreeGL" maxlength="20" size="6" value="%%GLOBAL_ProdPeachtreeGL%%" />
						</td>
					</tr>
				</table>
			</div><!-- End related products -->

			<!-- Start discount rules -->
			<div id="div7" style="padding-top: 10px;">
				<div style="padding-bottom:5px">%%LNG_DiscountRulesIntro%%</div>
				<div id="DiscountRulesWarning" class="MessageBox MessageBoxInfo" style="display: %%GLOBAL_HideDiscountRulesWarningBox%%;">%%GLOBAL_DiscountRulesWarningText%%</div>
				<div id="DiscountRulesDisplay" style="display: %%GLOBAL_DiscountRulesWithWarning%%;">
					<table class="Panel">
						<tr>
						  <td class="Heading2" colspan=2>%%LNG_DiscountRules%%</td>
						</tr>
					</table>
					<table class="Panel" id="DiscountRulesContainer">
					<tbody>
						%%GLOBAL_DiscountRules%%
					</tbody>
					</table>
				</div>
			</div><!-- End discount rules -->
			<!-- Start images & videos -->
			<div id="div8" style="padding-top: 10px;">
				<table class="Panel">
				<tr>
					<td class="Heading2" colspan="2">%%LNG_ProductImages%%</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="margin:10px 0 6px 10px;">%%LNG_ProductImagesIntroduction%%</div>

						<div style="margin:11px 0 6px 6px;">
							<button class="Button" id="productImagesDeleteSelected" disabled="disabled">%%LNG_DeleteSelected%%</button>
						</div>

						<table class="GridPanel productImagesTable" cellspacing="0">
							<tbody>
								<tr class="Heading3">
									<td class="productImageCheck" style="padding-left:5px;"><input type="checkbox" /></td>
									<td class="productImageThumbDisplay" style="width:%%GLOBAL_productImage_thumbnailWidth%%px;">%%LNG_Image%%</td>
									<td class="productImageDescription">%%LNG_Description%%</td>
									<td class="productImageBaseThumb"><span class="HelpText" onmouseout="HideQuickHelp(this)" onmouseover="ShowQuickHelp(this, '%%LNG_JS_UseAsBaseThumbnail%%', '%%LNG_JS_UseAsBaseThumbnailHelpText%%')">%%LNG_UseAsBaseThumbnail%%</span></td>
									<td class="productImageAction">%%LNG_Action%%</td>
								</tr>
							<tbody>
						</table>

						<div class="productImagesListItem" id="productImagesListItemNew">
							<table cellspacing="0">
								<tbody>
									<tr class="GridRowNoOver">
										<td class="productImageCheck"><input type="checkbox" /></td>
										<td class="productImageThumbDisplay" style="width:%%GLOBAL_productImage_thumbnailWidth%%px;">
											<div class="productImageNewPlaceholder" style="width:%%GLOBAL_productImage_thumbnailWidth%%px; height:%%GLOBAL_productImage_thumbnailHeight%%px;"><div style="line-height:%%GLOBAL_productImage_thumbnailHeight%%px;"><span>%%LNG_NewImage%%</span></div></div>
										</td>
										<td class="productImageDescription">
											<p>%%LNG_ProductImagesSelectUploadMethod%%</p>
											<ul>
												<li><span id="productImageNewUploadPlaceholder"></span><a id="productImageNewUpload" class="ProductImageNewUpload" href="#">%%LNG_UploadAnImage%%</a> <span class="SwfUploadAlternativeNotice" style="display:none;">%%LNG_ProductImagesBasicUploader%%</span></li>
												<li><a id="productImageNewWeb" href="#">%%LNG_UseImageFromWeb%%</a></li>
												<li><a id="productImageNewGallery" href="#">%%LNG_ChooseFromGallery%%</a></li>
											</ul>
										</td>
										<td class="productImageBaseThumb">&nbsp;</td>
										<td class="productImageAction">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>

						%%GLOBAL_productImagesList%%

					</td>
				</tr>
				</table>
				<table class="Panel">
				<tr>
					<td class="Heading2" colspan="2">%%LNG_YouTubeVideos%%</td>
				</tr>
				<tr>
					<td class="videoIntroRow" colspan="2">
						%%LNG_YouTubeVideosIntro%%
					</td>
				</tr>

				<tr>
					<td class="videoControlRow" colspan="2">
						<input type="text" class="Field250 exampleSearchText SetOriginalValue" id="searchYouTube" name="searchYouTube" value="%%LNG_VideoTypeToSearch%%" /> <input type="button" class="Button" value="%%LNG_VideoFindVideos%%" id="findVideosButton" />
					</td>
				</tr>

				<tr>
					<td class="videoControlRow" colspan="2">
						<div id="youtubeContainer">
							<div id="youtubeLeftBox" class="youtubeVideoListBox">
								<ul id="youTubeSearchVideos" style="display: none;"></ul>
								<div id="useSearchVideos">%%LNG_VideoUseSearchField%%</div>
								<div id="noSearchVideos" style="display: none;">%%LNG_VideoNoResults%%</div>
							</div>
							<div id="youtubeJoinBox">
								<input type="button" value="&raquo;" style="width: 40px;" id="addYouTubeVideos" /><br /><br />
								<input type="button" value="&laquo;" style="width: 40px;" id="removeYouTubeVideos" />
							</div>
							<div id="youtubeRightBox" class="youtubeVideoListBox">
								<ul id="youTubeCurrentVideos" style="display: none;"></ul>
								<div id="noCurrentVideos">%%LNG_VideoClickToAdd%%</div>
							</div>
						</div>
					</td>
				</tr>
				</table>
			</div><!-- End images & videos -->

			<!-- Start Google website optimizer -->
			<div id="div9">
				<p class="InfoTip" style="margin-bottom: 15px; margin-top: 15px;">%%GLOBAL_GoogleWebsiteOptimizerIntro%%</p>

				<table width="100%" class="Panel" style="margin-top: 0; padding-top: 0;  %%GLOBAL_ShowEnableGoogleWebsiteOptimzer%%">
					<tr>
						<td class="Heading2" colspan="2">%%LNG_GoogleWebsiteOptimizer%%</td>
					</tr>
					<tr>
						<td class="FieldLabel">
							%%LNG_EnableGoogleWebsiteOptimizer%%?
						</td>
						<td>
							<input %%GLOBAL_DisableOptimizerCheckbox%% type="checkbox" name="prodEnableOptimizer" id="prodEnableOptimizer" %%GLOBAL_CheckEnableOptimizer%% onclick = "ToggleOptimizerConfigForm(%%GLOBAL_SkipOpimizerConfirmMsg%%);" />
							<label for="prodEnableOptimizer">%%LNG_YesEnableGoogleWebsiteOptimizer%%</label>
						</td>
					</tr>
				</table>
				%%GLOBAL_OptimizerConfigForm%%
			</div>
			<!-- End Google website optimizer -->

			<table border="0" cellspacing="0" cellpadding="2" width="100%" class="PanelPlain" id="SaveButtons">
				<tr>
					<td>
						<input type="submit" disabled="disabled" value="%%LNG_SaveAndExit%%" class="FormButton" />
						<input type="submit" disabled="disabled" value="%%GLOBAL_SaveAndAddAnother%%" onclick="SaveAndAddAnother();" class="FormButton" style="width:130px" />
						<input type="reset" value="%%LNG_Cancel%%" class="FormButton" onclick="ConfirmCancel()" />
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
</div>
</form>

%%Panel.product_javascript%%

<div style="display:none;" id="UseImageFromWebDialog">
	<div class="ModalTitle">%%LNG_UseImageFromWebDialogTitle%%</div>
	<div class="ModalContent">
		<input type="text" class="Field100pct UseImageFromWebDialogImageUrl" name="imageurl[]" value="http://" /><br />
		<input type="text" class="Field100pct UseImageFromWebDialogImageUrl" name="imageurl[]" value="http://" /><br />
		<input type="text" class="Field100pct UseImageFromWebDialogImageUrl" name="imageurl[]" value="http://" /><br />
		<input type="text" class="Field100pct UseImageFromWebDialogImageUrl" name="imageurl[]" value="http://" /><br />
		<input type="text" class="Field100pct UseImageFromWebDialogImageUrl" name="imageurl[]" value="http://" /><br />
	</div>
	<div class="ModalButtonRow">
		<div class="FloatLeft">
			<img src="images/loading.gif" alt="" style="vertical-align: middle; display: none;" class="LoadingIndicator" />
			<input type="button" class="CloseButton FormButton" value="%%LNG_Close%%" onclick="$.modal.close();return false;" />
		</div>
		<input type="button" class="Submit" value="%%LNG_UseImages%%" onclick="ProductImages.useImageFromWebDialogSubmit();return false;" />
	</div>
</div><!-- end #UseImageFromWebDialog --><!-- located here to be outside of posted product info -->

<div id="QuickCategoryCreation" style="display: none;">
	<div class="ModalTitle">%%LNG_CreateACategory%%</div>
	<div class="ModalContent">
		<table class="Panel" width="100%">
			<tr>
				<td><strong>%%LNG_CatName%%:</strong></td>
			</tr>
			<tr>
				<td><input id="QuickCatName" type="text" name="catname" value=""  class="Field250" style="width: 100%;" /></td>
			</tr>
		</table>
		<div style="height: 4px; font-size: 1px;"></div>
		<table class="Panel" width="100%">
			<tr>
				<td><strong>%%LNG_CatParentCategory%%:</strong></td>
			</tr>
			<tr>
				<td>
					<select name="catparentid" size="8" id="QuickCatParent" style="width: 100%">
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="ModalButtonRow">
		<div class="FloatLeft">
			<img src="images/loading.gif" alt="" style="vertical-align: middle; display: none;" class="LoadingIndicator" />
			<input type="button" class="CloseButton FormButton" value="%%LNG_Cancel%%" onclick="$.modal.close();" />
		</div>
		<input type="button" class="Submit" value="%%LNG_Save%%" onclick="SaveQuickCategory()" />
	</div>
</div>
