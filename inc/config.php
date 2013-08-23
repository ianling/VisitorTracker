<?php
$generalSalt = "128 CHARACTER STRING GOES HERE";

$user = 'DB USER HERE';
$pass = 'DB PASSWORD HERE';
$db = new PDO('mysql:host=localhost;dbname=DB_NAME', $user, $pass);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>
