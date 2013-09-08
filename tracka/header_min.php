<?php
require_once('../inc/functions.php');
if(!isset($_SESSION['email'])){
    header('Location: '.$BASE_HOST);
}
$email = $_SESSION['email'];
$userID = $_SESSION['userID'];
