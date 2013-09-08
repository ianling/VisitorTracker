<?php
require_once('../inc/default_header.php');
if(!isset($_SESSION['email'])){
    header('Location: '.$BASE_HOST);
}
$email = $_SESSION['email'];
$userID = $_SESSION['userID'];
echo "<body>";
echo "<a href='index.php?page=1'>Index</a> - <a href='blocker.php'>Blocker</a> - <a href='settings.php?command=modifysites'>Settings</a> You're logged in as <b>$email</b>! - <a href='../logout.php'>LOGOUT</a><br><br>";
