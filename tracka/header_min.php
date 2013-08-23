<?php
require_once('../inc/functions.php');
if(!isset($_SESSION['email'])){
    gotoIndex();
}
$email = $_SESSION['email'];
$userID = $_SESSION['userID'];
