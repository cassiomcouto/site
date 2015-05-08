	<div class="BodyContainer">
	<table id="Table13" cellSpacing="0" cellPadding="0" width="100%">
		<tr>
			<td class="Heading1">%%LNG_ViewCoupons%%</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>%%GLOBAL_CouponIntro%%</p>
			%%GLOBAL_Message%%
			<table id="IntroTable" cellspacing="0" cellpadding="0" width="100%">
			<tr>
			<td class="Intro" valign="top">
				<input type="button" name="IndexAddButton" value="%%LNG_CreateCoupon%%..." id="IndexCreateButton" class="SmallButton" onclick="document.location.href='index.php?ToDo=createCoupon'" /> &nbsp;<input type="button" name="IndexDeleteButton" value="%%LNG_DeleteSelected%%" id="IndexDeleteButton" class="SmallButton" onclick="ConfirmDeleteSelected()" %%GLOBAL_DisableDelete%% />
			</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
		<td style="display: %%GLOBAL_DisplayGrid%%">
			<form name="frmCoupons" id="frmCoupons" method="post" action="index.php?ToDo=deleteCoupons">
				<div class="GridContainer">
					%%GLOBAL_CouponsDataGrid%%
				</div>
			</form>
		</td></tr>
	</table>
	</div>

	<script type="text/javascript">

		function ConfirmDeleteSelected()
		{
			var fp = document.getElementById("frmCoupons").elements;
			var c = 0;

			for(i = 0; i < fp.length; i++)
			{
				if(fp[i].type == "checkbox" && fp[i].checked)
					c++;
			}

			if(c > 0)
			{
				if(confirm("%%LNG_ConfirmDeleteCoupons%%"))
					document.getElementById("frmCoupons").submit();
			}
			else
			{
				alert("%%LNG_ChooseCoupons%%");
			}
		}

		function ToggleDeleteBoxes(Status)
		{
			var fp = document.getElementById("frmCoupons").elements;

			for(i = 0; i < fp.length; i++)
				fp[i].checked = Status;
		}

		function CouponClipboard(Data)
		{
			if (window.clipboardData)
			{
				window.clipboardData.setData("Text", Data);
				alert("%%LNG_CopiedClipboard%%");
			}
			else
			{
				alert("%%LNG_FeatureOnlyInIE%%");
			}
		}

	</script>