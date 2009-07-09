<?php
require_once('Plans.php');

function get_myprivl() {
	if (isset($_SESSION['glbs_lvl'])) {
		return $_SESSION['glbs_lvl'];
	} else {
		return 1;
	}
}
//////////
/*
setpriv - This function sets priviledge level
*/
function setpriv($myprivl, $cookpriv)
{
	$_SESSION['glbs_lvl'] = $myprivl;
}
//////////
/*
*Simply sets a plan that's on a person's autoread list to be marked as read
*/
function update_read($dbh, $owner, $updated)
{
	mysql_query("UPDATE autofinger SET updated = '0' WHERE owner = '$owner' and interest = '$updated'");
}
//////////
/*
*Marks when a person reads a plan
*/
function setReadTime($dbh, $idcookie, $interest)
{
	mysql_query("UPDATE autofinger SET readtime = NOW() WHERE owner = $idcookie AND interest = $interest");
}
function mark_as_read($dbh, $owner, $myprivl)
{
	$query = "UPDATE autofinger set updated = 0 where owner ='$owner' and priority = '$myprivl'";
	mysql_query($query);
}
function add_param($url, $name, $value)
{
	if (ereg($name, $url)) {
		return ereg_replace("$name=[^&]*", $name . '=' . $value, $url);
	} else {
		if (ereg("\?", $url)) {
			return $url . '&amp;' . $name . '=' . $value;
		} else {
			return $url . '?' . $name . '=' . $value;
		}
	}
}
function remove_param($url, $name)
{
	$url = ereg_replace("$name=[^&]*", '', $url);
	$url = preg_replace(array("@&$@"), array(''), $url);
	return $url;
}
?>
