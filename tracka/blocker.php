<?php
session_start();

if(!empty($_POST)) { //stuff was posted
    require_once('header_min.php');
    if(isset($_POST['newIP']) && isset($_POST['newJS'])) { //user posted a new entry
        $newIP = $_POST['newIP'];
        $newJS = $_POST['newJS'];
        //sanity check on the IP address
        $error = false;
        if(!sanityCheckIP($newIP)) {
            echo "ERROR: IP address ".$newIP." is invalid!";
            $error = true;
        }
        if(strlen($newJS) > 5000) {
            echo "ERROR: Too much javascript in ".$newIP."'s box (5000 characters max)!";
            $error = true;
        }
        $countEntriesQuery = $db->prepare("SELECT COUNT(id) FROM track_executor WHERE userID=?");
        $countEntriesQuery->execute(array($userID));
        $row = $countEntriesQuery->fetch();
        if($row['COUNT(id)'] >= 15) {
            echo "ERROR: You have too many blocker entries already.";
            $error = true;
        }
        if(!$error){
            $saveNewQuery = $db->prepare("INSERT INTO track_executor (userID,blockedIP,codeToRun) VALUES (?,?,?)");
            $saveNewQuery->execute(array($userID,$newIP,$newJS));
        }
    }
    elseif(isset($_POST['id']) && isset($_POST['ip']) && isset($_POST['js'])) {
        $idToUpdate = $_POST['id'];
		$ipToUpdate = $_POST['ip'];
		$jsToUpdate = $_POST['js'];

        //sanity checks
        $error = false;
        if(!sanityCheckIP($ipToUpdate)) {
            echo "ERROR: IP address ".$ipToUpdate." is invalid!";
            $error = true;
        }
        if(strlen($jsToUpdate) > 5000) {
            echo "ERROR: Too much javascript in ".$ipToUpdate."'s box (5000 characters max)!";
            $error = true;
        }
        if(!is_numeric($idToUpdate) || $idToUpdate < 0) { //this would only happen if they were altering POST's with some tool
            $error = true;
        }
        if(!$error){
		    $updateEntryQuery = $db->prepare("UPDATE track_executor SET blockedIP=?, codeToRun=? WHERE id=? AND userID=?");
            $updateEntryQuery->execute(array($ipToUpdate,$jsToUpdate,$idToUpdate,$userID));
	    }
    }
    elseif(isset($_POST['id']) && $_POST['delete'] === "DELETE ME") {
        $idToDelete = $_POST['id'];
        $removeEntryQuery = $db->prepare("DELETE FROM track_executor WHERE id=? AND userID=?");
        $removeEntryQuery->execute(array($idToDelete,$userID));
    }
}

else {
    require_once('header.php');
    echo "<link rel='stylesheet' type='text/css' href='index.css'>";
    echo "<script type='text/javascript' src='blocker.js'></script>";
    echo "<b>Add new:</b><br>
    <div class='newEntry'><input type='checkbox' class='entryCheckbox'><b>IP Address</b> (supports % as wildcard): <input class='ipBox' maxlength='15' placeholder='192.168.%'/><br>
    <span class='jsBoxLabel'><b>Javascript to Execute</b>: </span>
    <textarea class='jsBox' rows='9' cols='90'>
alert('Greetings!'); //Displays a pop-up box with the word 'Greetings!'

window.location.href='http://google.com'; //Redirects the user to Google

//Do not forget the semi-colon at the end of the statement, or else the code won't execute!</textarea></div><br><br>";

	echo "<b>Edit existing entries:</b><br><div class='existingEntries'>";
    $existingEntryGrabberQuery = $db->prepare("SELECT * FROM track_executor WHERE userID=? ORDER BY id ASC");
    $existingEntryGrabberQuery->execute(array($userID));
	while($row = $existingEntryGrabberQuery->fetch()) {
		echo "<div data-id='$row[id]' class='entry'><input type='checkbox' class='entryCheckbox'><b>IP Address</b>: <input value='$row[blockedIP]' class='ipBox' maxlength='15'/> <a class='remove-button'>[Remove Entry]</a><br>";
		echo "<span class='jsBoxLabel'><b>Javascript</b>: </span><textarea class='jsBox' rows='9' cols='80'>$row[codeToRun]</textarea><br><div class='divider'><hr></div></div>";
	}
	echo "</div><button class='save-button'>Save!</button> <img class='loadingImage' src='../inc/images/loading.gif'>";
}
