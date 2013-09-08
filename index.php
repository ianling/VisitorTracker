<?php
session_start();
require_once('inc/default_header.php');
include('inc/geoip/geoipregionvars.php');
include('inc/geoip/geoipcity.inc');

if(!isset($_SESSION['email'])) {
    $browserInfo = get_Browser(null, true); // Initiate info-getting function
    $ipAddress = $_SERVER['REMOTE_ADDR']; //ip
    $browser = $browserInfo['parent']; //browser
    $operatingSystem = $browserInfo['platform']; //OS
    $operatingSystem = str_replace("Win", "Windows ", $operatingSystem);
    $gi = geoip_open("inc/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
    $record = geoip_record_by_addr($gi,$ipAddress);
    $ipLocation = $record->city.", ".$GEOIP_REGION_NAME[$record->country_code][$record->region].", ".$record->country_name;
    geoip_close($gi);
    $giOrg = geoip_open('inc/geoip/GeoIPOrg.dat',GEOIP_STANDARD);
    $isp = geoip_org_by_addr($giOrg,$ipAddress);
    geoip_close($giOrg);


    echo "<link rel='stylesheet' type='text/css' href='tracka/index.css'>";
    echo "<script type='text/javascript' src='index.js'></script>";
	echo "MetalMetalLand Visitor Tracker<br><br>";
	echo "<a href='login/'>LOGIN</a> | <a href='register/'>REGISTER</a><br><br>";
    echo "<b>DEMO</b>:<br><div class='track'>".
	      date('F jS, Y g:i:s A')."<br>
          <div id='referrer'></div>
          <div id='currentPage'></div>
          <div id='ip'>IP: $ipAddress</div>
          <div id='isp'>ISP: $isp</div>
	      <div id='location'>Location: $ipLocation</div>
	      <div id='browser'>Browser: $browser on $operatingSystem</div>
	      <div id='res'>Resolution: </div>
	      </div>";
}
else {
    header("Location: ".$BASE_HOST."/tracka/?page=1");
}
?>
