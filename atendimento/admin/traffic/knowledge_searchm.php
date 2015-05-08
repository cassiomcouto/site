<?php
	$success = 0 ;
	$keyword_url = "" ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	$deptid = ( isset( $_GET['deptid'] ) ) ? $_GET['deptid'] : 0 ;
	$catid = ( isset( $_GET['catid'] ) ) ? $_GET['catid'] : 0 ;
	$page = ( isset( $_GET['pagex'] ) ) ? $_GET['pagex'] : 0 ;
	$questid = ( isset( $_GET['questid'] ) ) ? $_GET['questid'] : 0 ;
	$keyword = ( isset( $_GET['keyword'] ) ) ? $_GET['keyword'] : "" ;

	include_once( "../../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "../..", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting... [knowledge_searchm.php]</font>" ; exit ;
	}
	include_once("../../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util_Page.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/admin/traffic/APIknowledge/Util.php") ;
	include_once("$DOCUMENT_ROOT/admin/traffic/APIknowledge/get.php") ;
	include_once("$DOCUMENT_ROOT/admin/traffic/APIknowledge/put.php") ;
	include_once("$DOCUMENT_ROOT/admin/traffic/APIknowledge/update.php") ;

	if ( file_exists( "$DOCUMENT_ROOT/web/$l/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/$l/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	// conditions

	if ( $action == "rate" )
	{
		Knowledge_put_QuestionRating( $dbh, $x, $questid, $catid, $_GET['rating'] ) ;
		$action = "expand_question" ;
		$success = 1 ;
	}
	else if ( $action == "search" )
	{
		LIST( $deptid, $catid ) = explode( "<:>", $_GET['category'] ) ;
		Knowledge_put_SearchTerm( $dbh, $x, $keyword ) ;
	}

	$departments = AdminUsers_get_AllDepartments( $dbh, $x, 0 ) ;
	$total_questions = Knowledge_get_TotalASPQuestions( $dbh, $x ) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Knowledge BASE (FAQ) </title>

<link href="<?php echo $BASE_URL ?>/themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!--
	function do_alert()
	{
		if ( <?php echo $success ?> )
			alert( "<?php echo $LANG['KB_RATE_SUBMIT'] ?>" ) ;
	}

	function do_search()
	{
		if ( document.form_search.keyword.value == "" )
			alert( "<?php echo $LANG['KB_ERROR_KEYWORD'] ?>" ) ;
		else if ( document.form_search.keyword.value.length < 3 )
			alert( "<?php echo $LANG['KB_ERROR_KEYWORD_LEAST'] ?>" ) ;
		else
			document.form_search.submit() ;
	}

	function toggle_fill( the_text )
	{
		if ( document.form.canned.checked )
		{
			parent.window.writer.document.form.kb.value = 1 ;
			parent.window.writer.document.form.message.value = the_text ;
		}
		else
		{
			parent.window.writer.document.form.kb.value = 0 ;
			parent.window.writer.document.form.message.value = "" ;
		}
	}

//-->
</script>

</head>
<body class="faqbody">
<form method="GET" action="knowledge_searchm.php" name="form_search">
<table cellspacing="1">
<input type="hidden" name="action" value="search">
<input type="hidden" name="x" value="<?php echo $x ?>">
<input type="hidden" name="l" value="<?php echo $l ?>">
<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
<input type="hidden" name="category" value="0<:>0">
<tr valign="top"> 
	<td valign="center"><label for="keyword"><?php echo $LANG['WORD_KEYWORD'] ?>:</label></td>
	<td valign="center"><input name="keyword" type="text" id="keyword" value="<?php echo $keyword ?>"> <input type="button" value="<?php echo $LANG['WORD_SEARCH'] ?>" class="button" OnClick="do_search()"></td>
</tr>
</table>
</form>
<?php
	if ( $action == "expand_question" ):
	$questioninfo = Knowledge_get_QuestInfo( $dbh, $x, $questid ) ;
	$question = stripslashes( $questioninfo['question'] ) ;
	$answer = nl2br( stripslashes( $questioninfo['answer'] ) ) ;

	$ratinginfo = Knowledge_get_QuestRatingInfoIP( $dbh, $questid ) ;
	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $questioninfo['deptID'], $x ) ;
	Knowledge_update_QuestionClicks( $dbh, $x, $questid ) ;
?>
<div class="faq">
<div class="cookie"><a href="JavaScript:history.go(-1)">&laquo; <?php echo $LANG['WORD_BACK'] ?></a></b> | <a href="knowledge_searchm.php?x=<?php echo $x ?>&l=<?php echo $l ?>&deptid=<?php echo $deptinfo['deptID'] ?>"><?php echo $LANG['WORD_MAIN'] ?></a> &#8250; <a href="knowledge_searchm.php?l=<?php echo $l ?>&x=<?php echo $x ?>&action=expand_cat&deptid=<?php echo $deptinfo['deptID'] ?>&catid=0"><?php echo stripslashes( $deptinfo['name'] ) ?></a> <?php echo UtilKnowledge_PrintMenu( $dbh, $deptinfo['deptID'], $catid, "" ) ?></div>

<p class="question"><?php echo $LANG['WORD_QUESTION'] ?></p>
<p class="answer"><?php echo $question ?></p>

<br>

<form method="GET" action="knowledge_searchm.php" name="form">
<input type="hidden" name="action" value="rate">
<input type="hidden" name="l" value="<?php echo $l ?>">
<input type="hidden" name="x" value="<?php echo $x ?>">
<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
<input type="hidden" name="questid" value="<?php echo $questid ?>">
<input type="hidden" name="catid" value="<?php echo $catid ?>">
<p class="question"><?php echo $LANG['WORD_ANSWER'] ?></p>
<p class="answer"><i><?php echo $answer ?></i></p>
<br>
<hr>

		<?php if ( $ratinginfo['aspID'] ): ?>
		<?php echo $LANG['KB_RATE_SUBMIT'] ?>

		<?php else: ?>
		<p class="rate"><?php echo $LANG['KB_HELPFUL'] ?> <label for="rating1"><input type="radio" name="rating" value=1 OnClick="document.form.submit()" id="rating1"> <?php echo $LANG['WORD_YES'] ?></label> | <label for="rating0"><input type="radio" name="rating" value=0 OnClick="document.form.submit()" id="rating0"> <?php echo $LANG['WORD_NO'] ?></label></p>

		<?php endif ; ?>
</form>
</div>





<?php
	elseif ( $action == "expand_cat" ):
	$total_qs = Knowledge_get_TotalCatQuestions( $dbh, $x, $deptid, $catid ) ;
	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;

	if ( $catid )
	{
		$department = Knowledge_get_CatInfo( $dbh, $x, $catid ) ;
		$deptcats = Knowledge_get_ParentsCats( $dbh, $x, $catid ) ;
	}
	else
	{
		$department = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;
		$deptcats = Knowledge_get_DeptCats( $dbh, $x, $department['deptID'] ) ;
	}
	$name = stripslashes( $department['name'] ) ;
?>
<div class="faq">
<div class="cookie"><a href="JavaScript:history.go(-1)">&laquo; <?php echo $LANG['WORD_BACK'] ?></a></b> | <a href="knowledge_searchm.php?x=<?php echo $x ?>&l=<?php echo $l ?>&deptid=<?php echo $deptinfo['deptID'] ?>"><?php echo $LANG['WORD_MAIN'] ?></a> &#8250; <a href="knowledge_searchm.php?l=<?php echo $l ?>&x=<?php echo $x ?>&action=expand_cat&deptid=<?php echo $deptinfo['deptID'] ?>&catid=0"><?php echo stripslashes( $deptinfo['name'] ) ?></a>  <?php echo UtilKnowledge_PrintMenu( $dbh, $deptinfo['deptID'], $catid, "" ) ?></div>

<?php
	print "<ul> <li class=\"headcat\">$name</li>" ;

	$questions = Knowledge_get_CatQuestions( $dbh, $x, $department['deptID'], $catid, 0 ) ;
	for ( $q = 0; $q < count( $questions ); ++$q )
	{
		UtilKnowledge_PrintQuestion( $questions[$q] ) ;
	}
	print "</td></tr></table>" ;

	for ( $c2 = 0; $c2 < count( $deptcats ); ++$c2 )
	{
		$category = $deptcats[$c2] ;

		$total_qs = Knowledge_get_TotalCatQuestions( $dbh, $x, $category['deptID'], $category['catID'] ) ;
		$name = stripslashes( $category['name'] ) ;

		$full_list = "" ;
		if ( $total_qs > 5 )
			$full_list = "[ <a href=\"$BASE_URL/admin/traffic/knowledge_searchm.php?l=$l&x=$x&action=expand_cat&deptid=$category[deptID]&catid=$category[catID]\">+ $LANG[FULL_LIST] ($total_qs) ...</a> ]" ;
		print "<ul> <b>$name</b> &nbsp; $full_list" ;

		$questions = Knowledge_get_CatQuestions( $dbh, $x, $category['deptID'], $category['catID'], 5 ) ;
		for ( $q = 0; $q < count( $questions ); ++$q )
		{
			UtilKnowledge_PrintQuestion( $questions[$q] ) ;
		}
		UtilKnowledge_PrintSubCatsFolder( $dbh, $x, $category['catID'], 0 ) ;
		print "</ul>" ;
	}
?>
</div>










<?php
	elseif ( $action == "search" ):
	$keyword_url = preg_replace( "/ /", "+", $keyword ) ;
	$questions = Knowledge_get_KeywordSearchResults( $dbh, $x, $deptid, $catid, $keyword, $page, 15 ) ;
	$total_questions = Knowledge_get_TotalKeywordSearchResults( $dbh, $x, $deptid, $catid, $keyword ) ;
	$page_string = Page_util_CreatePageString( $dbh, $page, "knowledge_searchm.php?action=search&x=$x&l=$l&category=$_GET[category]&keyword=$keyword_url", 15, $total_questions ) ;
?>
<div class="faq">
<div class="cookie"><b><a href="knowledge_searchm.php?x=<?php echo $x ?>&l=<?php echo $l ?>"><?php echo $LANG['WORD_MAIN'] ?></a> &gt; <?php echo $LANG['WORD_SEARCH'] ?> <?php echo $LANG['WORD_KEYWORD'] ?>: <i><?php echo $keyword ?></i></b> &nbsp;&nbsp; (<?php echo $total_questions ?> <?php echo $LANG['RESULTS_FOUND'] ?>)</div>

<?php echo $LANG['WORD_PAGE'] ?>: <?php echo $page_string ?>
<ul>
<?php
	for ( $q = 0; $q < count( $questions ); ++$q )
	{
		UtilKnowledge_PrintQuestion( $questions[$q] ) ;
	}

	if ( count( $questions ) <= 0 )
		print "$LANG[KB_NO_RESULTS]<br>" ;
?>
</ul>
<?php echo $LANG['WORD_PAGE'] ?>: <?php echo $page_string ?>
</div>







<?php else: ?>
<h3>Categories</h3>
<div class="faq">
<?php

	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;

		$total_qs = Knowledge_get_TotalCatQuestions( $dbh, $x, $department['deptID'], 0 ) ;
		$name = stripslashes( $department['name'] ) ;

		$full_list = "" ;
		if ( $total_qs > 5 )
			$full_list = "[ <a href=\"$BASE_URL/admin/traffic/knowledge_searchm.php?l=$l&x=$x&action=expand_cat&deptid=$department[deptID]&catid=0\">+ $LANG[FULL_LIST] ($total_qs) ...</a> ]" ;

		print "\n<ul> <li class=\"headcat\">$name</li> &nbsp; $full_list\n" ;
		$questions = Knowledge_get_CatQuestions( $dbh, $x, $department['deptID'], 0, 5 ) ;
		for ( $q = 0; $q < count( $questions ); ++$q )
		{
			UtilKnowledge_PrintQuestion( $questions[$q] ) ;
		}

		$deptcats = Knowledge_get_DeptCats( $dbh, $x, $department['deptID'] ) ;
		for ( $c2 = 0; $c2 < count( $deptcats ); ++$c2 )
		{
			$category = $deptcats[$c2] ;
			$total_qs = Knowledge_get_TotalCatQuestions( $dbh, $x, $category['deptID'], $category['catID'] ) ;

			$name = stripslashes( $category['name'] ) ;

			$full_list = "" ;
			if ( $total_qs > 5 )
				$full_list = "[ <a href=\"$BASE_URL/admin/traffic/knowledge_searchm.php?l=$l&x=$x&action=expand_cat&deptid=$category[deptID]&catid=$category[catID]\">+ $LANG[FULL_LIST] ($total_qs) ...</a> ]" ;

			print "\n<ul> <li class=\"headcat\">$name</li> &nbsp; $full_list\n" ;
			$questions = Knowledge_get_CatQuestions( $dbh, $x, $category['deptID'], $category['catID'], 5 ) ;
			for ( $q = 0; $q < count( $questions ); ++$q )
			{
				UtilKnowledge_PrintQuestion( $questions[$q] ) ;
			}
			UtilKnowledge_PrintSubCatsFolder( $dbh, $x, $category['catID'], 0 ) ;
			print "\n</ul>\n" ;
			
		}
		print "\n</ul>\n" ;
	}
?>
</div>

<?php endif ; ?>



</body>
</html>