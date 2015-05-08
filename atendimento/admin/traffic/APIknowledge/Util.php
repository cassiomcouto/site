<?php
	if ( ISSET( $_OFFICE_GET_UtilKnowledge_LOADED ) == true )
		return ;

	$_OFFICE_UtilKnowledge_LOADED = true ;

	/*****

	   Internal Dependencies

	*****/
	include_once("$DOCUMENT_ROOT/admin/traffic/APIknowledge/get.php") ;

	/*****

	   Module Specifics

	*****/

	/*****

	   Module Functions

	*****/

	FUNCTION UtilKnowledge_PrintSubCats( $dbh,
							$aspid,
							$parentid,
							$counter )
	{
		if ( ( $aspid == "" ) || ( $parentid == "" ) )
		{
			return false ;
		}

		global $dept_cat_string ;

		$counter += 1 ;
		$spaces = 5 * $counter ;
		$tab_spaces = "" ;
		for ( $c = 0; $c < $spaces; ++$c )
			$tab_spaces .= "&nbsp;" ;

		$categories = Knowledge_get_ParentsCats( $dbh, $aspid, $parentid ) ;
		for ( $c = 0; $c < count( $categories ); ++$c )
		{
			$category = $categories[$c] ;
			$name = stripslashes( $category['name'] ) ;
			$selected = "" ;
			if ( $dept_cat_string == "$category[deptID]<:>$category[catID]" )
				$selected = "selected" ;

			if ( $parentid != $category['catID'] )
			{
				print "<option value=\"$category[deptID]<:>$category[catID]\" $selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tab_spaces - $name</option>" ;
				UtilKnowledge_PrintSubCats( $dbh, $aspid, $category['catID'], $counter ) ;
			}
		}
		return true ;
	}

	FUNCTION UtilKnowledge_PrintSubCatsFolder( $dbh,
							$aspid,
							$parentid,
							$counter )
	{
		if ( ( $aspid == "" ) || ( $parentid == "" ) )
		{
			return false ;
		}

		global $BASE_URL ;
		global $x ;
		global $l ;
		global $LANG ;

		$counter += 1 ;

		$categories = Knowledge_get_ParentsCats( $dbh, $aspid, $parentid ) ;
		for ( $c = 0; $c < count( $categories ); ++$c )
		{
			$category = $categories[$c] ;

			$total_qs = Knowledge_get_TotalCatQuestions( $dbh, $aspid, $category['deptID'], $category['catID'] ) ;
			$name = stripslashes( $category['name'] ) ;

			$full_list = "" ;
			if ( $total_qs > 5 )
				$full_list = "[ <a href=\"$BASE_URL/admin/traffic/knowledge_searchm.php?l=$l&x=$x&action=expand_cat&deptid=$category[deptID]&catid=$category[catID]\">+ $LANG[FULL_LIST] ($total_qs) ...</a> ]" ;

			print "\n<ul> <li class=\"headcat\">$name</li> &nbsp; $full_list\n" ;

			$questions = Knowledge_get_CatQuestions( $dbh, $aspid, $category['deptID'], $category['catID'], 5 ) ;
			for ( $q = 0; $q < count( $questions ); ++$q )
			{
				UtilKnowledge_PrintQuestion( $questions[$q] ) ;
			}
			UtilKnowledge_PrintSubCatsFolder( $dbh, $aspid, $category['catID'], $counter ) ;
			print "\n</ul>\n" ;
		}
		return true ;
	}

	FUNCTION UtilKnowledge_PrintSubCatsFolderAdmin( $dbh,
							$aspid,
							$parentid,
							$counter )
	{
		if ( ( $aspid == "" ) || ( $parentid == "" ) )
		{
			return false ;
		}

		global $BASE_URL ;
		global $x ;
		global $l ;

		$counter += 1 ;
		$categories = Knowledge_get_ParentsCats( $dbh, $aspid, $parentid ) ;
		for ( $c = 0; $c < count( $categories ); ++$c )
		{
			$category = $categories[$c] ;

			$name = stripslashes( $category['name'] ) ;

			print "\n<ul> <b>$name</b> [<a href=\"knowledge_config.php?action=edit_cat&catid=$category[catID]\">edit</a>] [<a href=\"JavaScript:remove_cat( $category[catID] )\">remove</a>]" ;

			$questions = Knowledge_get_CatQuestions( $dbh, $aspid, $category['deptID'], $category['catID'], 0 ) ;
			for ( $q = 0; $q < count( $questions ); ++$q )
			{
				UtilKnowledge_PrintQuestionAdmin( $questions[$q] ) ;
			}
			UtilKnowledge_PrintSubCatsFolderAdmin( $dbh, $aspid, $category['catID'], $counter ) ;
			print "</ul>\n" ;
		}
		return true ;
	}

	FUNCTION UtilKnowledge_PrintQuestion( $data )
	{
		global $BASE_URL ;
		global $x ;
		global $l ;
		global $keyword_url ;

		$question = stripslashes( $data['question'] ) ;
		print "<li> <a href=\"$BASE_URL/admin/traffic/knowledge_searchm.php?l=$l&x=$x&action=expand_question&questid=$data[questID]&deptid=$data[deptID]&catid=$data[catID]&keyword=$keyword_url\">$question</a></li>" ;
		return true ;
	}

	FUNCTION UtilKnowledge_PrintQuestionAdmin( $data )
	{
		global $BASE_URL ;

		$question = stripslashes( $data['question'] ) ;
		$question = preg_replace( "/</", "&lt;", $question ) ;
		$question = preg_replace( "/>/", "&gt;", $question ) ;
		print "<li> $question<br>(help index: $data[points])<br>[<a href=\"knowledge_config.php?action=edit_quest&questid=$data[questID]\">edit</a>] [<a href=\"JavaScript:remove_question( $data[questID] )\">remove</a>]</li>" ;
		return true ;
	}

	FUNCTION UtilKnowledge_PrintMenu( $dbh, $deptid, $catid, $string_in )
	{
		global $BASE_URL ;
		global $x ;
		global $l ;

		$catinfo = Knowledge_get_CatInfo( $dbh, $x, $catid ) ;
		if ( isset( $catinfo['name'] ) )
		{
			$name = stripslashes( $catinfo['name'] ) ;

			$string = " &#8250; <a href=\"knowledge_searchm.php?l=$l&x=$x&action=expand_cat&deptid=$deptid&catid=$catinfo[catID]\">$name</a>" ;

			if ( $catinfo['catID_parent'] )
				$string = UtilKnowledge_PrintMenu( $dbh, $deptid, $catinfo['catID_parent'], $string ) ;

			return "$string $string_in" ;
		}
		return "" ;
	}

?>