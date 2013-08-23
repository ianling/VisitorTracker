<?php
session_start();
require_once('header.php');
echo "<link rel='stylesheet' type='text/css' href='index.css'>";
echo "<script type='text/javascript' src='index.js'></script>";
$command = $_GET['command'];
if(!isset($command) && !isset($_POST['save']))
    gotoIndex();
else if ($command == "modifysites") { //adding a site to the user's account
    $siteListQuery = $db->prepare("SELECT * FROM track_sites WHERE userID=?");
    $siteListQuery->execute(array($userID));
    echo "<b>Sites:</b><br>";
    echo "<div class='siteList'>";
    while($siteList = $siteListQuery->fetch()) {
        echo "<div class='siteEntry'>";
        $siteIndex = $siteList['id']; //testing
        echo "<i>".htmlspecialchars($siteList['siteName'])."</i> - <a class='select-code-button'>[Select Code]</a>
                                                                   <a href='settings.php?command=renamesite&site=$siteIndex'>[Rename]</a>
                                                                   <a href='settings.php?command=removesite&site=$siteIndex'>[Remove]</a><br>";
        echo "<div class='code'>
            <pre>
&lt;!-- Begin MetalMetalLand Tracker Code --&gt;

&lt;script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.10.2.min.js\"&gt;&lt;/script&gt;
&lt;script type=\"text/javascript\" src=\"https://www.metalmetalland.com/tracker/trackerJS.php?userID=$userID&site=$siteIndex\"&gt;&lt;/script&gt;

&lt;!-- End MetalMetalLand Tracker Code --&gt;</pre>
        </div></div><br>";
    } //All existing sites have been displayed
    echo "</div>";

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
    header("Location: https://www.metalmetalland.com/tracker/tracka/settings.php?command=modifysites");
}

//REMOVE SITE
else if($command == "removesite" && isset($_GET['site'])) {
    $removeSiteQuery = $db->prepare("DELETE FROM track_sites WHERE userID=? AND id=?");
    $removeSiteQuery->execute(array($userID,$_GET['site']));
    $removeSiteVisitsQuery = $db->prepare("DELETE FROM track_visitList WHERE userID=? AND siteID=?");
    $removeSiteVisitsQuery->execute(array($userID,$_GET['site']));
    $removeSiteVisitorsQuery = $db->prepare("DELETE FROM track_visitors WHERE userID=? AND siteID=?");
    $removeSiteVisitorsQuery->execute(array($userID,$_GET['site']));

    header("Location: https://www.metalmetalland.com/tracker/tracka/settings.php?command=modifysites");
}

//RENAME SITE
else if($command == "renamesite" && isset($_GET['site']) ) { //TODO: Make a form, possible with jQuery above where the user can input a new name
    $renameSiteQuery = $db->prepare("UPDATE track_sites SET siteName=? WHERE userID=? AND id=?");
    //$renameSiteQuery->execute(array($))
}