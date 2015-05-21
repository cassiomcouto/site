<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11">
<html>
<head>
	<title>%%LNG_ControlPanel%%</title>
	<meta http-equiv="Content-Type" content="text/html; charset=%%GLOBAL_CharacterSet%%" />
	<meta name="robots" content="noindex, nofollow" />
	<style type="text/css">
		@import url("Styles/styles.css");
		@import url('Styles/tabmenu.css');
		@import url("Styles/iselector.css");
	</style>
	<!--[if IE]>
	<style type="text/css">
		@import url("Styles/ie.css");
	</style>
	<![endif]-->
	<script type="text/javascript" src="../javascript/jquery.js?%%GLOBAL_JSCacheToken%%"></script>
	<link rel="stylesheet" href="Styles/thickbox.css?%%GLOBAL_JSCacheToken%%" type="text/css" media="screen" />
</head>

<body>
	<div id="box">
		<table><tr><td style="border:solid 2px #DDD; padding:20px; background-color:#FFF; width:300px">
			<table>
				<tr>
					<td class="Heading1">
						<a href="index.php">%%GLOBAL_AdminLogo%%</a>
					</td>
				</tr>
				<tr>
					<td style="padding:0 0 5px 0">
						<p>
							<strong>%%LNG_PleaseCheckYourInbox%%</strong>
						</p>
						<p>
							%%GLOBAL_Message%%
						</p>
					</td>
				</tr>
			</table>
		</td></tr></table>
	</div>

	<script type="text/javascript">

		function sizeBox() {
			var w = $(window).width();
			var h = $(window).height();
			$('#box').css('position', 'absolute');
			$('#box').css('top', h/2-($('#box').height()/2)-50);
			$('#box').css('left', w/2-($('#box').width()/2));
		}

		$(document).ready(function() {
			sizeBox();
		});

		$(window).resize(function() {
			sizeBox();
		});

	</script>

</body>
</html>