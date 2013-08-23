<?php
session_start();

if(isset($_POST['portToScan']) && isset($_POST['ipToScan'])) {
    require_once('header_min.php');

    $errorScanning = false;
	if(!is_numeric($_POST['portToScan']) || $_POST['portToScan'] > 65535 || $_POST['portToScan'] < 1) { //Port can only contain numbers, commas, and dashes
		echo "ERROR: Couldn't scan port ".$_POST['portToScan'];
        print_r($_POST['portToScan']);
        $errorScanning = true;
	}
	if(!filter_var($_POST['ipToScan'],FILTER_VALIDATE_IP)) {
		echo "Invalid IP address.";
        $errorScanning = true;
	}
    if(!$errorScanning) {
        $ipToScan = $_POST['ipToScan'];
        $port = $_POST['portToScan'];

	    $message = "";
        $message = $message . portScan($ipToScan, $port); //portScan() returns a string ("Port 532 is open on $ip!")
        echo $message;
    }
}
else {
    require_once('header.php');
    echo "<link rel='stylesheet' type='text/css' href='index.css'>";
    echo "<script type='text/javascript' src='scanner.js'></script>";

    echo "<a href='statistics.php?ip=$_GET[ip]'>Click here to go back to this IP's stats page</a><br><br>";
    echo "<div class='scannerForm'>";
    echo "IP Address (no wildcards): <input id='ipBox' name='ipToScan' value='$_GET[ip]' maxlength='15'/><br>";
    echo "Ports: <input id='portsBox' name='ports' value='80,8080,21-22' maxlength='25'/><br>";
    echo "<button class='scan-button'>Scan!</button></div>";
    echo "<div class='scanResults'></div>";
}