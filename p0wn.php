<?php
header("Access-Control-Allow-Origin: *");
require_once('inc/functions.php');
include('inc/geoip/geoipregionvars.php');
include('inc/geoip/geoipcity.inc');

$userID = $_POST['id']; //the ID of the user whose page got visited
$siteID = $_POST['site'];

if(!isset($userID) || !isset($siteID) || !is_numeric($siteID) || !is_numeric($userID) || !isset($_POST['currentpage']) || !is_numeric($_POST['screenh']) || !is_numeric($_POST['screenw'])){
	die();
}

date_default_timezone_set('Europe/London'); //UTC
$ipAddress = $_SERVER['REMOTE_ADDR']; //ip
//FIRST--- Make sure this IP isn't being excluded; we don't need to waste time doing this stuff if they are excluded.
$exclusionCheckQuery = $db->prepare("SELECT exclusions FROM track_sites WHERE id=? AND userID=?");
$exclusionCheckQuery->execute(array($siteID,$userID));
$exclusionsRow = $exclusionCheckQuery->fetch();
$exclusions = str_replace(" ","",$exclusionsRow['exclusions']);
$exclusions = str_replace("%","*",$exclusionsRow['exclusions']);
$exclusions = explode(",", $exclusions);
foreach($exclusions as $exclusion)
    if(fnmatch($exclusion,$ipAddress))
        die();


$browserInfo = get_Browser(null, true); // Initiate info-getting function
$browser = $browserInfo['parent']; //browser
$operatingSystem = $browserInfo['platform']; //OS
$operatingSystem = str_replace("Win", "Windows ", $operatingSystem);
$unixTime = $_SERVER['REQUEST_TIME'];
$currentPage = $_POST['currentpage']; //Current Page
$referrer = $_POST['ref']; //referrer

//GEOIP STUFF
$gi = geoip_open("inc/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
$record = geoip_record_by_addr($gi,$ipAddress);
$ipLocation = $record->city.", ".$GEOIP_REGION_NAME[$record->country_code][$record->region].", ".$record->country_name;
geoip_close($gi);
$giOrg = geoip_open('inc/geoip/GeoIPOrg.dat',GEOIP_STANDARD);
$isp = geoip_org_by_addr($giOrg,$ipAddress);
geoip_close($giOrg);
//format: Birmingham, Alabama, US

$screenH = $_POST['screenh'];
$screenW = $_POST['screenw'];

$insertionQuery = $db->prepare("INSERT INTO track_visitList (userID,siteID,ipAddress,browser,operatingSystem,unixTime,ipLocation,ISP,referrer,currentPage,screenH,screenW) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
$insertionQuery->execute(array($userID,$siteID,$ipAddress,$browser,$operatingSystem,$unixTime,$ipLocation,$isp,$referrer,$currentPage,$screenH,$screenW));