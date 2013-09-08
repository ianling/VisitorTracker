<?php
$BASE_PROTOCOL = 'https'; //http or https. REQUIRED
$BASE_SUBDOMAIN = 'www.'; //with trailing dot. Can be left blank
$BASE_HOSTNAME = 'example.com'; //include www if applicable. No trailing slash. REQUIRED!
$BASE_PATH = '/tracker'; //no trailing slash. Can be left blank.

//Adds up to: https://www.example.com/tracker
$BASE_HOST = $BASE_PROTOCOL."://".$BASE_SUBDOMAIN.$BASE_HOSTNAME.$BASE_PATH; //You probably don't need to touch this.

//Generate a 128-character random string for use in a salting passwords
$generalSalt = "128 CHARACTERS GO HERE!";

//DATABASE SETTINGS -- ADJUST AS NEEDED
$user = 'user';
$pass = 'pass';
$db = new PDO('mysql:host=localhost;dbname=tracker', $user, $pass);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>
