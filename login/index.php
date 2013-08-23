<?php
if(!isset($_POST['save'])) { //we haven't posted anything yet
    echo "<link rel='stylesheet' type='text/css' href='../tracka/index.css'>";
    echo '<b>Loggin\' in.</b><br>
    <form action="index.php" method="post">
	    Email Address: <input id="email" name="email" maxlength="50" placeholder="email@emailemail.net"/> <br>
	    Password: <input type="password" id="password" name="pass" maxlength="30"/> <br>
	<br>
	<input type="submit" value="Onward!" name="save" />
    </form><br>
    Haven\'t registered? Why did you click the \'Login\' button? <a href="../register/">Click here to go to the registration page.</a>';
}

else { //the user has posted data
    session_start();

    if (empty($_POST["email"]) || empty($_POST["pass"])) { //...but they didn't post an email or password
        die("How do you forget to fill in the boxes? There are only two. <a href='index.php'>Go back and do it right.</a>");
    }

    else if (isset($_POST["email"]) && isset($_POST["pass"])) { //They've posted all the info they need to
        require_once('../inc/functions.php');

        $email = strtolower($_POST["email"]);

        $userRowQuery = $db->prepare("SELECT * FROM track_users WHERE email=?");
        $userRowQuery->execute(array($email));
        if($userRow = $userRowQuery->fetch())
            $id = $userRow['id'];
        else
            die("The email or password you entered is wrong.");

        $passwordFromDB = $userRow['password'];
        $saltFromDB = $userRow['salt'];
        $hashedPass = hashPassword($_POST['pass'],$saltFromDB,$generalSalt); //hashin' some passes

        if($hashedPass != $passwordFromDB)
            die("The email or password you entered is wrong.");


        //$accountActivated = $row3['activated'];
        //if($accountActivated == "1") {

            //At this point, if it hasn't died, everything is alright.
            //Time to add session vars.
            $_SESSION['email'] = $email;
            $_SESSION['userID'] = $id;
            $_SESSION['timezone'] = $userRow['timezone'];
            //Send the session key to the database for checking purposes
            //$upload_the_key = mysql_query("UPDATE track_users SET sessionKay='$sessionkeyf0rdb' WHERE id='$id'");

            gotoIndex();
        //}
        //else if($activated_or_not == "0") {
        //    die("The account has not yet been activated! Check your Spam folder if you didn't receive the activation email!");

        //}
    }
}

