<h2>Config Settings Check</h2>
<?php
	if ( file_exists( "../web/conf-init.php" ) )
	{
		include_once("../web/conf-init.php") ;
		$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
		include_once("../system.php") ;
		include_once("../web/VERSION_KEEP.php") ;
		include_once("$DOCUMENT_ROOT/API/sql.php") ;
	}
	else
	{
		print "Configuration File: <font color=\"#FF0000\"><b>ERROR conf-init.php not found in web/ directory</b></font><br>" ;
	}
?>
Checking Session Directory (<font color="#B87F23"><?php echo session_save_path() ?></font>): 
<?php if ( is_writable( session_save_path() ) ): ?>
<font color="#29C029"><b>Passed!</b></font>
<?php else: ?>
<font color="#FF0000"><b>Failed - Please allow full read/write permission on dir <?php echo session_save_path() ?></b></font>
<?php endif ; ?>
<br>
Document Root: <font color="#B87F23"><?php echo ( isset( $DOCUMENT_ROOT ) ) ? $DOCUMENT_ROOT : "<font color=\"#FF0000\"><b>ERROR</b></font>" ?></font><br>
Base URL: <font color="#B87F23"><?php echo ( isset( $BASE_URL ) ) ? $BASE_URL : "<font color=\"#FF0000\"><b>ERROR</b></font>" ?></font><br>
<hr>
<h2>PHP Info</h2>
<?php print phpinfo() ; ?>