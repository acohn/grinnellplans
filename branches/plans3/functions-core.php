<?php
require_once('Plans.php');
require_once ('dbfunctions.php');

function isvaliduser($dbh, $username)
{
        if (!get_items($mydbh, "username", "accounts", "username", $username)) {
                return 0;
        } else {
                return 1;
        }
}
?>