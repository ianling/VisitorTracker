<?php
$BASE_PROTOCOL = 'https'; //http or https. REQUIRED
$BASE_SUBDOMAIN = 'www'; //No trailing dot. Can be left blank
$BASE_HOSTNAME = 'example.com'; //No trailing slash. REQUIRED!
$BASE_PATH = '/tracker'; //With leading slash! can be left blank

$BASE_HOST = $BASE_PROTOCOL."://".$BASE_SUBDOMAIN.".".$BASE_HOSTNAME.$BASE_PATH; //DON'T TOUCH.
//so it all adds up to https://www.example.com/tracker

//Generate a 128-character random string for use in a salting passwords
$generalSalt = "SALT";

//DATABASE SETTINGS -- ADJUST AS NEEDED
$user = 'USER';
$pass = 'PASSWORD';
$db = new PDO('mysql:host=localhost;dbname=tracker', $user, $pass);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>
