<?php
require_once('inc/functions.php');
$activator_key = mysql_real_escape_string($_GET['JrKQ']);

if(strlen($activator_key) != 8) {
	die("Invalid activation link.");
}
else{
	$update_active_status = mysql_query("UPDATE track_users SET activated='1' WHERE activationkey='$activator_key'");
	echo "Activated!<br><a href=\"index.php\">Back to the Index</a>";
	}
?>
