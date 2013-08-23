<?php
require_once('functions.php');
echo "<head><title>MetalMetalLand Tracker</title>";
echo "<script type='text/javascript' src='https://www.metalmetalland.com/tracker/inc/jquery.js'></script>";
if($_SERVER['REMOTE_ADDR'] != "67.171.148.51")
    echo "<script type='text/javascript' src='https://www.metalmetalland.com/tracker/trackerJS.php?userID=2&site=9'></script>";
echo "</head>";
