<?php
require_once ("../Plans.php");
$username = $_POST['username'];
require ("auth.php");
?>

<html>
<html>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
if ($mysubmit) {
	$motd = preg_replace("/\n/s", "<br>", $motd);
	$motd = addslashes($motd);
	mysql_query("UPDATE system SET motd = '$motd'");
	echo $motd;
} //if a username
{ //if no username
	//$addrow = array("My message","");
	//add_row($dbh, "system", $addrow);
	//$motd = get_item($dbh,"motd","system","username", $username);
	$my_result = mysql_query("Select motd From system");
	$my_row = mysql_fetch_array($my_result);
	$motd = $my_row[0];
	$motd = ereg_replace("<br>", "", $motd);
	$motd = stripslashes($motd);
?>
<form action="changemotd.php" method="POST">
<textarea name="motd" cols="100" rows="40" >
<?php echo $motd
?>
</textarea>
<input type="hidden" name="mysubmit" value="1">
<input type="submit" value="Change MOTD">
</form>
<?php
}
db_disconnect($dbh);
?>
</html>
</body>
