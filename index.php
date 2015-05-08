<?php
include(dirname(__FILE__)."/init.php");

	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == "tracking_script") {
			$visitor = GetClass('ISC_VISITOR');
			$visitor->OutputTrackingJavascript();
		}
		else if($_REQUEST['action'] == "track_visitor") {
			$visitor = GetClass('ISC_VISITOR');
			$visitor->TrackVisitor();
		}
	}
    RewriteIncomingRequest();
?>