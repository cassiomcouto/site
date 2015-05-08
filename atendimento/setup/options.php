<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util_Optimize.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/get.php") ;
	$section = 0;			// Section number - see header.php for list of section numbers

	$current_phplive_version = "3.2.2" ;

	// auto detect if correct version is used... if not, redict to patch
	if ( $PHPLIVE_VERSION != $current_phplive_version )
	{
		HEADER( "location: patches/" ) ;
		exit ;
	}

	$nav_line = '';
?>
<?php
	// initialize

	// update all admins status to not available if they have been idle
	AdminUsers_update_IdleAdminStatus( $dbh, $admin_idle ) ;

	$now = mktime( 0,0,0,date("m"),date("j"),date("Y") ) ;
	$oldest_footprintstat_date = ServiceFootprint_get_LatestFootprintStatDate( $dbh, $session_setup['aspID'] ) ;
	
	if ( $oldest_footprintstat_date )
		$oldest_footprintstat_date = mktime( 0,0,0,date("m", $oldest_footprintstat_date),date("j", $oldest_footprintstat_date),date("Y", $oldest_footprintstat_date) ) ;
	// > 0 because if there is no data, database spits out negative numbers
	if ( ( $oldest_footprintstat_date < $now ) && ( $oldest_footprintstat_date > 0 ) && !isset( $session_setup['daylight'] ) )
	{
		if ( isset( $_GET['timestamp'] ) && ( $_GET['timestamp'] == $now ) )
		{
			// daylight savings has errors
			$_SESSION['session_setup']['daylight'] = 1 ;
		}
		$month = date("m", $oldest_footprintstat_date ) ;
		$day = date("j", $oldest_footprintstat_date ) ;
		$year = date("Y", $oldest_footprintstat_date ) ;
		HEADER( "location: optimize.php?month=$month&day=$day&year=$year&timestamp=$oldest_footprintstat_date" ) ;
		exit ;
	}
	else
	{
		if ( isset( $_GET['optimized'] ) )
		{
			$tables = ARRAY( "chat_admin", "chatcanned", "chatdepartments", "chatfootprints", "chatfootprintsunique", "chatrequestlogs", "chatrequests", "chatsessionlist", "chatsessions", "chattranscripts", "chatuserdeptlist" ) ;

			if ( !preg_match( "/(chatsupportlive.com)|(osicode.com)|(osicodes.com)|(osicodes.net)|(phplivesupport.com)|(phplivesupportasp.com)|(phplivesupport.net)|(ositalk.com)|(phproi.com)|(phpliveasp.com)|(phplive.net)/", $_SERVER['SERVER_NAME'] ) )
			{
				Util_OPT_Database( $dbh, $tables ) ;
			}
			HEADER( "location: options.php?view" ) ;
			exit ;
		}
	}
?>
<?php include_once("./header.php"); ?>
<style type="text/css">
.inset {   background:transparent;   width:100%;   } .inset h1, .inset p {   margin:0 10px;   } .inset h1 {   font-size:2em; color:#fff;   } .inset p {   padding-bottom:0.5em;   } .inset .b1, .inset .b2, .inset .b3, .inset .b4, .inset .b1b, .inset .b2b, .inset .b3b, .inset .b4b {   display:block;   overflow:hidden;   font-size:1px;   } .inset .b1, .inset .b2, .inset .b3, .inset .b1b, .inset .b2b, .inset .b3b {   height:1px;   } .inset .b2 {   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #aaa;   } .inset .b3 {   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #ddd;   } .inset .b4 {   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #eee;   } .inset .b4b {   background:#E7E7E7;   border-left:1px solid #aaa;   border-right:1px solid #fff;   } .inset .b3b {   background:#E7E7E7;   border-left:1px solid #ddd;   border-right:1px solid #fff;   } .inset .b2b {   background:#E7E7E7;   border-left:1px solid #eee;   border-right:1px solid #fff;   } .inset .b1 {   margin:0 5px;   background:#999;   } .inset .b2, .inset .b2b {   margin:0 3px;   border-width:0 2px;   } .inset .b3, .inset .b3b {   margin:0 2px;   } .inset .b4, .inset .b4b {   height:2px; margin:0 1px;   } .inset .b1b {   margin:0 5px;   background:#fff;   } .inset .boxcontent {   display:block;   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #fff;   }
</style>
<table width="100%" border="0" cellspacing="15" cellpadding="0">
<tr>
	<td valign="top" align="center" colspan=3 width="100%">
		
		<table cellspacing=0 cellpadding=2 border=0>
		<tr>
			<td valign="top">
				<div class="inset">
				<b class="b1"></b><b class="b2"></b><b class="b3"></b><b class="b4"></b>
				<div class="boxcontent">
				<table cellspacing=0 cellpadding=2 border=0>
				<tr>
					<td nowrap><span class="basicTitle">:: Inicial ::</span><br>
						<?php if ( file_exists( "../super/asp.php" ) && isset( $ASP_KEY ) && $ASP_KEY ): ?>
						<li> <a href="customize.php?action=logo">Atualizar Logo Marca</a></li>
						<?php endif ; ?>
						<li> <a href="adddept.php">Criar Departamentos</a></li>
						<li> <a href="adduser.php">Criar Operadores</a></li>
						<li> <a href="code.php">Gerar HTML</a></li>
					</td>
				</tr>
				</table>
				</div>
				<b class="b4b"></b><b class="b3b"></b><b class="b2b"></b><b class="b1b"></b>
				</div>
			</td>
			<td width=1>&nbsp;</td>
			<td valign="top">
				<table cellspacing=1 cellpadding=2 border=0>
				<tr>
					<td><span class="basicTitle">Operador Login URL</span><br>
						<br>
					</td>
				</tr>
				<tr>
					<td><a href="<?php echo $BASE_URL ?>/web/<?php echo $session_setup['login'] ?>/" class="basicTitle" target="new"><font color="#29C029"><strong><?php echo $BASE_URL ?>/web/<?php echo $session_setup['login'] ?>/</font></a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		
	 </td>
</tr>
<tr><td height="2" colspan=3 class="hdash"><img src="../images/spacer.gif" width="1" height="2"></td></tr>
  <tr>
	<td width="33%" align="center"><table width="208" border="0" cellspacing="0">
		<tr> 
		  <td width="208" height="138" valign="top" background="../images/g_manage.jpg"><br> 
			<a href="manager.php"><span class="panelTitle">Gerenciar</span></a><br>
			
			<a href="adddept.php" class="sectionLink">Departamentos</a><br>
			<a href="adduser.php" class="sectionLink">Gerenciar Operadores</a><br>
			<a href="code.php" class="sectionLink">Gerar HTML</a>
			</td>
		</tr>
	  </table></td>
	  <td width="33%" align="center"><table width="208" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="208" height="138" valign="top" background="../images/g_interface.jpg"><br> 
			<a href="interface.php"><span class="panelTitle">Interface</span></a><br>

			<a href="customize.php?action=themes" class="sectionLink">Chat Temas</a><br>
			<a href="customize.php?action=icons" class="sectionLink">Chat Icones</a><br>
			<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
			<a href="customize.php?action=initiate" class="sectionLink">Imagem Inicial do Chat</a>
			<?php endif ; ?>
			</td>
		</tr>
	  </table></td>
	  <td width="33%" align="center"> <table width="208" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="208" height="138" valign="top" background="../images/g_prefs.jpg"><br> 
			<a href="prefs.php"><span class="panelTitle">Preferencias</span></a><br>

			<a href="prefs.php?action=footprints" class="sectionLink">Excluir IP Tracking</a><br>
			<a href="email_transcript.php" class="sectionLink">Email Transcripts</a><br>
			<a href="prefs.php?action=timezone" class="sectionLink">Time Zone</a>
			</td>
		</tr>
	  </table></td>
</tr>
<tr>
	<td width="33%" align="center"><table width="208" border="0" cellspacing="0">
		<tr> 
		  <td width="208" height="138" valign="top" background="../images/g_profile.jpg"><br> 
			<a href="profiles.php"><span class="panelTitle">Operador Configs/Reports</span></a><br>
			
			<a href="statistics.php" class="sectionLink">Pedido de Suporte</a><br>
			<a href="opratings.php" class="sectionLink">Avaliação</a><br>
			<a href="profiles.php?action=pics" class="sectionLink">Imagens</a>
			</td>
		</tr>
	  </table></td>
	<td width="33%" align="center"> <table width="208" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="208" height="138" valign="top" background="../images/g_sessions.jpg"><br> 
			<a href="sessions.php"><span class="panelTitle">Sessoes</span></a><br>

			<a href="processes.php?action=chat" class="sectionLink">Chats Atuais</a><br>
			<a href="transcripts.php" class="sectionLink">Chat Transcripts</a><br>
			<a href="processes.php?action=consol" class="sectionLink">Admin Console</a>
			</td>
		</tr>
	  </table></td>
		<td width="33%" align="center"><table width="208" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="208" height="138" valign="top" background="../images/g_reports.jpg"><br> 
			<a href="reports.php"><span class="panelTitle">Traffic Reports</span></a><br>

			<a href="footprints.php" class="sectionLink">Traffic &amp; Footprints</a><br>
			<a href="refer.php" class="sectionLink">Refer URLs</a>
			</td>
		</tr>
	  </table></td>
</tr>
<tr>
	<td width="33%" align="center"><table width="208" border="0" cellspacing="0">
		<tr>
			<td width="208" height="138" valign="top" background="../images/g_prefs.jpg"><br>
			<a href="chatprefs.php"><span class="panelTitle">Chat Preferencias</span></a><br>

			<a href="chatprefs.php?action=polling" class="sectionLink">Polling Time</a><br>
			<a href="chatprefs.php?action=polling_type" class="sectionLink">Polling Tipo</a><br>
			<a href="chatprefs.php?action=language" class="sectionLink">Idioma</a><br>
			</td>
		</tr>
	  </table></td>
	<?php if ( $INITIATE && ( file_exists( "$DOCUMENT_ROOT/admin/traffic/click_track.php" ) || file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/salespath.php" ) ) ): ?>
	<td width="33%" align="center">
		<table width="208" border="0" cellspacing="0">
		<tr>
			<td width="208" height="138" valign="top" background="../images/g_marketing.jpg"><br>
			<a href="marketing.php"><span class="panelTitle">Publicidades</span></a><br>

			<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/click_track.php" ) ): ?>
			<a href="../admin/traffic/click_track.php" class="sectionLink">Click Track'it</a><br>
			<!-- <a href="../admin/traffic/click_conversion.php" class="sectionLink">Track'it Conversion</a><br> -->
			<?php endif ; ?>
			</td>
		</tr>
		</table>
	</td>
	<?php endif ; ?>
	<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge.php" ) ): ?>
	<td width="33%" align="center">
		<table width="208" border="0" cellspacing="0">
		<tr> 
			<td width="208" height="138" valign="top" background="../images/g_knowledge.jpg"><br> 
			<a href="../admin/traffic/knowledge.php"><span class="panelTitle">Knowledge Base</span></a><br>

			<a href="../admin/traffic/knowledge_config.php" class="sectionLink">Preferencias</a>
			<a href="../admin/traffic/knowledge_config.php?action=config" class="sectionLink">Configurar</a>
			</td>
		</tr>
		</table>
	</td>
	<?php endif ; ?>
</tr>

</table>
<?php include("footer.php") ; ?>
