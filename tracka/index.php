<?php
session_start();
require_once('header.php');
echo "<link rel='stylesheet' type='text/css' href='index.css'>";
echo "<script type='text/javascript' src='index.js'></script>";

$timezone = $_SESSION['timezone'];
$pageNumber = (isset($_GET['page']) ? $_GET['page'] : 1);
$allSites = array(); //An array containing all of the user's sites
$getSitesQuery = $db->prepare("SELECT * FROM track_sites WHERE userID=?");
$getSitesQuery->execute(array($userID));
while($row = $getSitesQuery->fetch()) {
    $allSites[] = $row; //fill the array with arrays of the user's sites
}

if(!isset($pageNumber) || !is_numeric($pageNumber) || strlen($pageNumber) < 1 || strlen($pageNumber) > 2 || $pageNumber < 1) {
	$pageNumber = 1; //set page number to 1 if they entered something weird like 0 or 999
}
if(count($allSites) == 0) //They just registered or something!
    header("Location: https://www.metalmetalland.com/tracker/tracka/settings.php?command=modifysites");

if(count($_GET['sites']) == 0 && count($allSites) != 0)  //They didn't select anything, but they had stuff to choose from
    $selectedSites[] = $allSites[0]['id']; //This will also happen the first time the page is loaded after logging in or something

else { //Sanity check for the user's inputted sites
    $badSiteInArray = 0;
    foreach($selectedSites as &$siteSelected) {
        if(!is_numeric($siteSelected))
            $badSiteInArray = 1;
        if($siteSelected < 1)
            $badSiteInArray = 1;
    }
    if($badSiteInArray == 1)
        $selectedSites[] = $allSites[0]['id']; //Reset their selected sites cuz they did something wrong
    else //Everything is a-OK
        $selectedSites = $_GET['sites'];

}

//Now we can start the actual page, everything has been taken care of
//Need to escape these just in case because PDO can't handle arrays, could be vulnerable to SQLi
foreach($selectedSites as &$unescapedSite)
    $unescapedSite = mysql_real_escape_string($unescapedSite);

$siteIDsJoined = join(',',$selectedSites);

$visitsTodayQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND siteID in ($siteIDsJoined) AND convert_tz(FROM_UNIXTIME(unixTime), 'UTC', ?) >= DATE(convert_tz(NOW(), 'UTC', ?));");
$visitsTodayQuery->execute(array($userID,$timezone,$timezone));
$visitsToday = $visitsTodayQuery->fetch();
$visitsToday = $visitsToday['COUNT(id)'];

$visitsThisWeekQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND siteID in ($siteIDsJoined) AND YEARWEEK(convert_tz(FROM_UNIXTIME(unixTime), 'UTC', ?)) = YEARWEEK(convert_tz(NOW(), 'UTC', ?));");
$visitsThisWeekQuery->execute(array($userID,$timezone,$timezone));
$visitsThisWeek = $visitsThisWeekQuery->fetch();
$visitsThisWeek = $visitsThisWeek['COUNT(id)'];

$visitsThisMonthQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? AND siteID in ($siteIDsJoined) AND MONTH(convert_tz(NOW(), 'UTC', ?)) = MONTH(convert_tz(FROM_UNIXTIME(unixTime), 'UTC', ?)) AND YEAR(NOW())=YEAR(FROM_UNIXTIME(unixTime));");
$visitsThisMonthQuery->execute(array($userID,$timezone,$timezone));
$visitsThisMonth = $visitsThisMonthQuery->fetch();
$visitsThisMonth = $visitsThisMonth['COUNT(id)'];

$visitsTotalQuery = $db->prepare("SELECT COUNT(id) FROM track_visitList WHERE userID=? and siteID in ($siteIDsJoined)");
$visitsTotalQuery->execute(array($userID));
$visitsTotal = $visitsTotalQuery->fetch();
$visitsTotal = $visitsTotal['COUNT(id)'];

$uniqueVisitsQuery = $db->prepare("SELECT COUNT(Distinct ipAddress) as theCount FROM track_visitList WHERE userID=? AND siteID in ($siteIDsJoined);");
$uniqueVisitsQuery->execute(array($userID));
$uniqueVisits = $uniqueVisitsQuery->fetch();
$uniqueVisits = $uniqueVisits['theCount'];


echo "<div class='totals'>";
echo "Visits to selected sites by <b>all IP's</b>:<br>";
echo "<b>Today</b>: $visitsToday<br>";
echo "<b>This week</b>: $visitsThisWeek<br>";
echo "<b>This month</b>: $visitsThisMonth<br>";
echo "<b>Total</b>: $visitsTotal<br>";
echo "<b>Unique visitors</b>: $uniqueVisits</div>";

//The checkboxes for selecting which sites to include
echo "<div class='siteSelection'><b>Sites:</b><br><form action='index.php' method='get'>";
foreach($allSites as &$site) {
    echo "<label><input type='checkbox' name='sites[]' value='".$site['id']."' ";
    if (in_array($site['id'],$selectedSites))
        echo "checked";
    echo "/> ".$site['siteName'];
    echo "</label><br>";
}
echo "<input type='submit'>"; //TODO: Make this AJAX, no submit button.
echo "</form></div>";

$resultsMin = 1+20*($pageNumber-1);
$resultsMax = 20*$pageNumber;
echo "<br><br><br><div id='container'>You're on page <b>$pageNumber</b> of your visits. Currently viewing visits <b>$resultsMin-$resultsMax</b>.";
echo " <a href=index.php?page=";
$nextPage = $pageNumber+1;
echo "$nextPage>Older Visits</a> / ";
if($pageNumber == 1) {
	echo "<i>Newer Visits</i><br>";
}else{
	echo "<a href=index.php?page=";
	$previousPage = $pageNumber-1;
	echo "$previousPage>Newer Visits</a><br>";
}
$queryResultsMin = $resultsMin-1;

$trackGetterQuery = $db->prepare("SELECT * FROM track_visitList WHERE userID=? AND siteID IN ($siteIDsJoined) ORDER BY id DESC LIMIT ?, 20");
$trackGetterQuery->execute(array($userID,$queryResultsMin));
while($row = $trackGetterQuery->fetch()){
    $siteName = "";
    $date = new DateTime("@".$row['unixTime']);
    $userTZ = new DateTimeZone($timezone);
    $date->setTimezone($userTZ);
    $siteID = $row['siteID'];
    foreach($allSites as &$site) {
        if($siteID === $site['id']) {
            $siteName = $site['siteName'];
            break 1;
        }
    }
	echo "<div class='divider'><hr></div>
          <div class='track'>".
	      $date->format('F jS, Y g:i:s A')."<br>
          Site: <i>$siteName</i><br>
	      Referrer: <a href='$row[referrer]'>$row[referrer]</a><br>
	      Landed: <a href='$row[currentPage]'>$row[currentPage]</a><br>
	      IP: <a href='statistics.php?ip=$row[ipAddress]'>$row[ipAddress]</a> <span class='optionsButton'></span><br>
	      ISP: $row[ISP]<br>
	      Location: $row[ipLocation]<br>
	      Browser: $row[browser] on $row[operatingSystem]<br>
	      Resolution: $row[screenW]x$row[screenH]<br>
	      </div><br>";
}
echo "<div class='divider'><hr></div>";
echo " <a href=index.php?page=";
$nextPage = $pageNumber+1;
echo "$nextPage>Older Visits</a> / ";
if($pageNumber == 1) {
    echo "<i>Newer Visits</i><br>";
}else{
    echo "<a href=index.php?page=";
    $previousPage = $pageNumber-1;
    echo "$previousPage>Newer Visits</a><br>";
}
require_once('footer.php');