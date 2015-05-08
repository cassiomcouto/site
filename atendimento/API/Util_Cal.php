<?php
	if ( ISSET( $_OFFICE_UTIL_CAL_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_CAL_LOADED = true ;

	function Util_Cal_DrawCalendar( $dbh,
						$m,
						$y,
						$href,
						$href_self,
						$href_month,
						$action )
	{
		global $TIMEZONE ;
		if ( ( !$m ) || ( !$y ) )
		{ 
			$m = date( "m",time()+$TIMEZONE ) ;
			$y = date( "Y",time()+$TIMEZONE ) ;
		}
		$today_m = date( "m",time()+$TIMEZONE ) ;
		$today_y = date( "Y",time()+$TIMEZONE ) ;
		$today_d = date( "j",time()+$TIMEZONE ) ;

		// get the weekday of the first
		$tmpd = getdate( mktime( 0,0,0,$m,1,$y ) ) ;
		$month = $tmpd["month"]; 
		$firstwday= $tmpd["wday"];
		$lastday = LastDayOfMonth( $m, $y ) ;
	?>
	<table cellpadding=1 cellspacing=2 border=0 width="180">
	<tr class="altcolor1"><td colspan=7>
		<table cellpadding=3 cellspacing=0 border=0 width="100%">
			<tr>
				<th width="30">
					<a href="<?php echo$href_self?>&m=<?php echo(($m-1)<1) ? 12 : $m-1 ?>&y=<?php echo(($m-1)<1) ? $y-1 : $y ?>&d=1&action=<?php echo $action ?>" class="nav">&lt;&lt;</a> &nbsp;
					<a href="<?php echo$href_self?>&m=<?php echo$m?>&y=<?php echo((($m-1)<1) ? $y-1 : $y)-1?>&d=1&action=<?php echo $action ?>" class="nav">&lt;</a></th>
				<th align="center">
				<?php if ( $href_month ): ?>
				<a href="<?php echo"$href_month&m=$m&y=$y"?>" class="nav"><?php echo"$month $y"?></a></font>
				<?php else: ?>
				<?php echo"$month $y"?></font>
				<?php endif ; ?>
				</th>
				<th width="30" align="right">
					<a href="<?php echo$href_self?>&m=<?php echo$m?>&y=<?php echo((($m+1)>12) ? $y+1 : $y)+1 ?>&d=1&action=<?php echo $action ?>" class="nav">&gt;</a> &nbsp;
					<a href="<?php echo$href_self?>&m=<?php echo(($m+1)>12) ? 1 : $m+1 ?>&y=<?php echo(($m+1)>12) ? $y+1 : $y ?>&d=1&action=<?php echo $action ?>" class="nav">&gt;&gt;</a>
				</th>
			</tr>
		</table>
	</td></tr>
	<tr align="center" class="altcolor1"><td><strong>Su</td><td><strong>M</td>
		<td><strong>T</td><td><strong>W</td>
		<td><strong>Th</td><td><strong>F</td>
		<td><strong>Sa</td></tr>
	<?php 
		$d = 1;
		$wday = $firstwday;
		$firstweek = true;

		// loop through days of the week
		while ( $d <= $lastday ) 
		{
			// put blank fillers for the first week exmptys
			if ( $firstweek )
			{
				print "<tr align=\"center\">" ;
				for ( $i=1; $i <= $firstwday; $i++ ) 
					print "<td><font size=2>&nbsp;</font></td>";
				$firstweek = false;
			}

			// each sunday, (0), we place a new row
			if ( $wday == 0 )
				print "<tr align=\"center\">" ;

			$class = "class=\"altcolor2\"" ;
			$time_begin = mktime( 0, 0, 0, $m, $d, $y ) ;
			$time_end = mktime( 23, 59, 59, $m, $d, $y ) ;

			if ( ( $today_d == $d ) && ( $today_m == $m ) && ( $today_y == $y ) )
				$class = "class=\"altcolor1\"" ;

			// the ouput of calendar
			print "
				<td $class><a href=\"$href&m=$m&d=$d&y=$y\">$d</a></td>
			" ;

			// end the <tr> on a Saturday
			if ( $wday == 6 )
				print "</tr>\n" ;

			$wday++;
			$wday = $wday % 7;
			$d++;
		}
	?>
	</tr></table>
	<?php
	} 

	function LastDayOfMonth( $mon, $year )
	{
		return date( "d", mktime( 23,59,59,$mon+1,0,$year ) ) ;
	}
?>