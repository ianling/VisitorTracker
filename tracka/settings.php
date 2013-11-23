<?php
session_start();
require_once('header.php');
echo "<link rel='stylesheet' type='text/css' href='index.css'>";
$command = $_GET['command'];
if ($command == "modifysites") { //adding a site to the user's account
    echo "<script type='text/javascript' src='settings.js'></script>";
    echo "<script type='text/javascript' src='../inc/jquery.jeditable.mini.js'></script>";
    $siteListQuery = $db->prepare("SELECT * FROM track_sites WHERE userID=?");
    $siteListQuery->execute(array($userID));
    echo "<b>Sites:</b><br>";
    echo "<div class='siteList'>";
    while($siteList = $siteListQuery->fetch()) {
        $siteIndex = $siteList['id']; //testing
        $exclusionList = str_replace("%","*",$siteList['exclusions']);
        echo "<div class='siteEntry'>";
        echo "<span data-site-id='$siteIndex' style='font-style:italic; cursor: pointer;text-decoration: underline;' class='site-name'>".htmlspecialchars($siteList['siteName'])."</span> - <a class='select-code-button'>[Select Code]</a>
                                                                   <a href='settings.php?command=removesite&site=$siteIndex'>[Remove]</a><br>";
        echo "<div class='code'>
            <pre>
&lt;!-- Begin Visitor Tracker Code --&gt;

&lt;script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.10.2.min.js\"&gt;&lt;/script&gt;
&lt;script type=\"text/javascript\" src=\"".$BASE_HOST."/trackerJS.php?userID=$userID&site=$siteIndex\"&gt;&lt;/script&gt;

&lt;!-- End Visitor Tracker Code --&gt;</pre>
        </div>";
        echo "<div data-site-id='$siteIndex' class='exclusions'><span class='exclusions-box-label'>Excluded IP's: </span><textarea rows=3 cols=50 class='exclusions-box' placeholder='Comma-separated list of IP addresses. * is wildcard: 192.168.*, 67.141.33.251, etc.'>$exclusionList</textarea>";
        echo "<input type='checkbox' class='exclusionsCheckbox'></div></div>";
    } //All existing sites have been displayed
    echo "</div><button class='save-button'>Save!</button><img class='loadingImage' src='../inc/images/checkmark.png'>";
    echo "</div><br>";

    echo "<br><b>Add a Site to Track</b>:<br>
          <form action='settings.php?command=addsite' method='post'>
            Site Name: <input id='sitename' name='sitename' maxlength='30' placeholder='My Blog'/><br>
            <input type='submit' value='Add Site' name='save'>
          </form>";
    echo "<br>Note: In order to actually track a site, you need to put the tracker code in the website.<br>
          Additionally, Adblock+'s EasyPrivacy list will opt people out of tracking, and will also cause parts of this website<br>
          to stop functioning. There are no ads on any MetalMetalLand websites, so you may as well disable Adblock.";

}

//ADD SITE
else if($command == "addsite" && isset($_POST['sitename']) && isset($_POST['save'])) {
    if(strlen($_POST['sitename']) > 30)
        die("Invalid site name.");

    $getSiteCountQuery = $db->prepare("SELECT COUNT(*) AS total FROM track_sites WHERE userID=?");
    $getSiteCountQuery->execute(array($userID));
    if($row = $getSiteCountQuery->fetch())
        if($row['total'] == 5)
            die("You can only have five sites.");

    $getSitesQuery = $db->prepare("SELECT * FROM track_sites WHERE userID=? AND siteName=?");
    $getSitesQuery->execute(array($userID,$_POST['sitename']));
    if($row = $getSitesQuery->fetch())
        die("A site with that name already exists.");
    //At this point, all possible errors are accounted for, put it in the DB.

    $insertNewSiteQuery = $db->prepare("INSERT INTO track_sites (userID,siteName) VALUES (?,?)");
    $insertNewSiteQuery->execute(array($userID,$_POST['sitename']));
    header("Location: ".$BASE_HOST."/tracka/settings.php?command=modifysites");
}

//REMOVE SITE
else if($command == "removesite" && isset($_GET['site'])) {
    $removeSiteQuery = $db->prepare("DELETE FROM track_sites WHERE userID=? AND id=?");
    $removeSiteQuery->execute(array($userID,$_GET['site']));
    $removeSiteVisitsQuery = $db->prepare("DELETE FROM track_visitList WHERE userID=? AND siteID=?");
    $removeSiteVisitsQuery->execute(array($userID,$_GET['site']));

    header('Location: '.$BASE_HOST.'/tracka/settings.php?command=modifysites');
}

//RENAME SITE
else if(isset($_POST['siteName']) && isset($_POST['siteID'])) {
    if(strlen($_POST['siteName']) > 30)
        die();
    if(!is_numeric($_POST['siteID']))
        die();
    $renameSiteQuery = $db->prepare("UPDATE track_sites SET siteName=? WHERE userID=? AND id=?");
    $renameSiteQuery->execute(array($_POST['siteName'],$userID,$_POST['siteID']));
}

//EDIT EXCLUDED IP's
else if(isset($_POST['siteID']) && isset($_POST['ipList'])) {
    if(!is_numeric($_POST['siteID']))
        die();
    if(strlen($_POST['ipList']) > 500)
        die();
    $ipList = str_replace("*","%",$_POST['ipList']);
    $editExcludedListQuery = $db->prepare("UPDATE track_sites SET exclusions=? WHERE userID=? AND id=?");
    $editExcludedListQuery->execute(array($ipList,$userID,$_POST['siteID']));
}

//ADD LABEL TO IP
else if(isset($_POST['ipaddress']) && isset($_POST['textcolor']) && isset($_POST['bgcolor'])) {
    $ip = $_POST['ipaddress'];
    $textColor = $_POST['textcolor'];
    $bgColor = $_POST['bgcolor'];
    $label = $_POST['label'];
    //sanity check their stuff
    if(!filter_var($ip,FILTER_VALIDATE_IP))
        die(); //this will never happen unless they're tampering with the POST
    if(strlen($textColor) != 6 || strlen($bgColor) != 6)
        die();
    if(strlen($label) > 30)
        die();

    $countExistingLabels = $db->prepare("SELECT count(id) as total FROM track_labels WHERE userID=?");
    $countExistingLabels->execute(array($userID));
    if($row = $countExistingLabels->fetch())
        if ($row['total'] > 700)
            die(); //Too many existing labels, limited to 700.

    $checkForExistingLabel = $db->prepare("SELECT id FROM track_labels WHERE userID=? AND ipAddress=?");
    $checkForExistingLabel->execute(array($userID,$ip));
    if($row = $checkForExistingLabel->fetch()) { //a row for this IP already exists
        $editLabelQuery = $db->prepare("UPDATE track_labels SET label=?, color=?, textColor=? WHERE id=?"); //UPDATE IT!
        $editLabelQuery->execute(array($label,$bgColor,$textColor,$row['id']));
    }
    else {
        $addLabelQuery = $db->prepare("INSERT INTO track_labels (userID,ipAddress,label,color,textColor) VALUES (?,?,?,?,?)");
        $addLabelQuery->execute(array($userID,$ip,$label,$bgColor,$textColor));
    }

}

//RESET A LABEL
else if (isset($_POST['ipaddress']) && $_POST['command'] === "RESET") {
    $ip = $_POST['ipaddress'];
    if(!filter_var($ip,FILTER_VALIDATE_IP))
        die(); //this will never happen unless they're tampering with the POST

    $removeLabelQuery = $db->prepare("DELETE FROM track_labels WHERE userID=? AND ipAddress=?");
    $removeLabelQuery->execute(array($userID,$ip));
}

//They've done something strange
else header('Location: '.$BASE_HOST);