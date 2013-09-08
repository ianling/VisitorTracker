<?php
require_once('config.php');

function loggedIn() {
    if(isset($_SESSION['userID']))
        return true;
    return false;
}

function generateSalt() {
    return mcrypt_create_iv(128, MCRYPT_DEV_URANDOM);
}
function hashPassword($password, $userSalt, $generalSalt) {
    $hash = hash('whirlpool',$generalSalt.$password.$userSalt); //hashin' some passes
    for ($i = 1; $i < 25001; $i++) {
        if ($i % 2 == 0)
            $hash = hash('whirlpool',$hash);
        else
            $hash = hash('sha384',$hash);
    }
    return $hash;
}

function sanityCheckIP($ip) {
    if(strlen($ip) < 1 || strlen($ip) > 15 || preg_match('/[^\d\.%]/',$ip) || $ip == "") //match any character except numbers, dots, and percent signs
        return false;
    return true;
}

function portScan($ip, $port){
    $message = "";
    if ($port > 65535 || $port < 1)
        $message = $message . "Port <b>" . $port . "</b> is out of bounds (1-65535).";
    else {
        $fp = @fsockopen($ip,$port,$errno,$errstr,0.6);
        socket_set_nonblock($fp);
        while($fp !== false && !$fp) {}
        if($fp) {
            fclose($fp);
            $message = "Port <b>" . $port . "</b> is <FONT COLOR='00B00F'>open</FONT> on <b>" . $ip . "</b><br>";
        }
        else{
            $message = "Port <b>" . $port . "</b> is <FONT COLOR='C20000'>closed</FONT> on <b>" . $ip . "</b><br>";
        }
        flush();
    }
    return $message;
}

?>