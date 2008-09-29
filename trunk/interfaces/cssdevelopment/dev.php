<?php

/*
GrinnellPlans
Interface - CSS, Development
Created by: Sechyi Laiu, 3/22/2006
Version .1
Version .2 after examining Brantley's code 6/12/06
*/

function disp_begin($dbh,$idcookie,$myurl,$myprivl,$cssloc,$jsfile)
	{
	if (!$myprivl == 2 or !$myprivl == 3)
	 {$myprivl = 1;}

	 $searchname = $_GET['searchname'];
	 if ($searchname) {
	    $title = "[$searchname]'s Plan";
		} else {
	    $title = "Plans Alpha CSS";
		 }
?>
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOARCHIVE">
<title><?php echo $title ?></title>
<link rel=stylesheet
href="<?=$cssloc?>">

<?
if ( !is_null( $jsfile ) )
    echo "<script language=\"javascript\" type=\"text/javascript\" src=\"$jsfile\"></script>";
?>

</head>
<body>

<div id="main">
<div id="controls">
	<div id="logo">
	</div>
	
	<div id="ReadForm">
		<form action="read.php" method="get">
  		<fieldset>
			<input name="searchname" type="text"><br>
  			<input type="hidden" name="myprivl" value="<? echo $myprivl; ?>">
  			<input type="submit" value="Read"></form>
		</fieldset>
		</form>
	</div>

	<div id="Navigation">
		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><a href="edit.php?myprivl=<? echo $myprivl;?>" class="main">edit plan</a></td>
		</tr>

		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><a href="listusers.php?myprivl=<? echo $myprivl;?>" class="main">list users</a></td>
		</tr>

		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><a href="search.php?myprivl=<? echo $myprivl;?>"
		class="main">search plans</a></td>
		</tr>

		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><a href="customize.php?myprivl=<? echo $myprivl;?>" class="main">preferences</a></td>
		</tr>

		<?
		if (wants_secrets($idcookie)) {
		    $count = count_unread_secrets($idcookie);
		?>

		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><a href="anonymous.php">Secrets (<?=$count?>)</a></td>
		</tr>

		<?
		} else {
		    // do nothing
		}

		$linkarray = mysql_query("Select avail_links.html_code
		From avail_links, opt_links where avail_links.static = 'yes' and opt_links.userid = '$idcookie' and opt_links.linknum = avail_links.linknum");
		    while($new_row = mysql_fetch_row($linkarray)) {
		      $linklist[] = $new_row;
		    }
		$o=0;
		while ($linklist[$o][0])
		{
		?>
		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><?=$linklist[$o][0]?></td>
		</tr>
		<?
		 $o++;}
		?>

		<tr>
		<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
		<td><a href="index.php?logout=1" class="main">log out</a></td>
		</tr>
	</div>
<? //close the controls css division
//and support legacy tables for now....
?>
</div>
</table></td></tr></table></td>

<? //close the main css division
?>
</div>

<div id="autoread">
	<p class="main">auto read list</p>
	<?
	autoread_list($myurl, $idcookie, $myprivl);
	?>
</div>

<?
//and let's close mdisp_begin!
//and support legacy start of middle table
?>
<table<tr><td>

<?
}

?>

<? //css division for autoread settings and enddisplay may be redundant
?>
		<?
		function mdisp_end($dbh,$idcookie,$myurl,$myprivl)
			{
			?>
			<div id="autoreadsettings">
			<?
			if (!$myprivl == 2 and !$myprivl == 3)
			 {$myprivl = 1;}
		?>	
	</div>

<? //division for disclaimer here may be redundant
?>
	<p id="disclaimer">
	<?
	footer();
	?>
	</p>

</body></html>
<?
//and let's close mdisp_end
}
?>
