<?php
session_start();
require_once('header.php');
echo "<link rel='stylesheet' type='text/css' href='index.css'>";
echo "<script type='text/javascript' src='index.js'></script>";

//sanity checks for the inputted IP
$ipToGet = $_GET['ip'];
if(!sanityCheckIP($ipToGet))
    die("Invalid IP address!");
$timezone = $_SESSION['timezone'];

$visitsTodayQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND ipAddress like ? AND convert_tz(FROM_UNIXTIME(unixTime), 'UTC', ?) >= DATE(convert_tz(NOW(), 'UTC', ?));");
$visitsTodayQuery->execute(array($userID,$ipToGet,$timezone,$timezone));
$visitsToday = $visitsTodayQuery->fetch();
$visitsToday = $visitsToday['COUNT(id)'];

$visitsThisWeekQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND ipAddress like ? AND YEARWEEK(convert_tz(FROM_UNIXTIME(unixTime), 'UTC', ?)) = YEARWEEK(convert_tz(NOW(), 'UTC', ?));");
$visitsThisWeekQuery->execute(array($userID,$ipToGet,$timezone,$timezone));
$visitsThisWeek = $visitsThisWeekQuery->fetch();
$visitsThisWeek = $visitsThisWeek['COUNT(id)'];

$visitsThisMonthQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND ipAddress like ? AND MONTH(convert_tz(NOW(), 'UTC', ?)) = MONTH(convert_tz(FROM_UNIXTIME(unixTime), 'UTC', ?)) AND YEAR(NOW())=YEAR(FROM_UNIXTIME(unixTime));");
$visitsThisMonthQuery->execute(array($userID,$ipToGet,$timezone,$timezone));
$visitsThisMonth = $visitsThisMonthQuery->fetch();
$visitsThisMonth = $visitsThisMonth['COUNT(id)'];

$visitsTotalQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND ipAddress like ?");
$visitsTotalQuery->execute(array($userID,$ipToGet));
$visitsTotal = $visitsTotalQuery->fetch();
$visitsTotal = $visitsTotal['COUNT(id)'];

echo "<div class='totals'>
Visits to selected sites by <b>$ipToGet</b>:<br>
<b>Today</b>: $visitsToday<br>
<b>This week</b>: $visitsThisWeek<br>
<b>This month</b>: $visitsThisMonth<br>
<b>Total</b>: $visitsTotal<br></div>";
echo "<div class='toolbox'><b>Tools</b>:<br>";
if (strstr($ipToGet,"%"))
    echo "<i>Port Scanner</i>";
else
    echo "<a href='scanner.php?ip=$ipToGet'>Port Scanner</a>";
echo "<br><br><br><br></div><br>";


$visitListGetterQuery = $db->prepare("SELECT * FROM track_visitList WHERE userID=? AND ipAddress LIKE ? ORDER BY id DESC LIMIT 0, 30");
$visitListGetterQuery->execute(array($userID,$ipToGet));
while($row = $visitListGetterQuery->fetch()) {
    $date = new DateTime("@".$row['unixTime']);
    $userTZ = new DateTimeZone($timezone);
    $date->setTimezone($userTZ);

    echo "<div class='divider'><hr></div>
          <div class='track'>".
          $date->format('F jS, Y g:i:s A')."<br>
	      Referrer: <a href='$row[referrer]'>$row[referrer]</a><br>
	      Landed: <a href='$row[currentPage]'>$row[currentPage]</a><br>
	      IP: $row[ipAddress]<br>
	      ISP: $row[ISP]<br>
	      Location: $row[ipLocation]<br>
	      Browser: $row[browser] on $row[operatingSystem]<br>
	      Resolution: $row[screenW]x$row[screenH]<br>
	      </div><br>";
}

require_once('footer.php');