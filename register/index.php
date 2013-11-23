<head><title>Visitor Tracker</title></head>
<?php
require_once("../inc/ayah/ayah.php");
$ayah = new AYAH();
if(isset($_POST['save'])) {
    require_once('../inc/functions.php');

    $ayahScore = $ayah->scoreResult();
    if ($ayahScore) { //THE AYAH TEST WENT WELL!
        if ((empty($_POST["email"]) || empty($_POST["pass"]) || empty($_POST["confirmpass"]) || empty($_POST["tzdropdown"])) && isset($_POST["save"]))
        {
            die("Error: You screwed something up. Make sure your email and password are valid, and that you confirmed your password.");
        }
        if (isset($_POST["save"]) && isset($_POST["pass"]) && isset($_POST["email"]) && isset($_POST["confirmpass"])) {
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                die("Invalid email address entered.");
            //Make sure the email address is valid.

            require_once('../inc/config.php');
            if($_POST["pass"] != $_POST["confirmpass"])
                die("Your passwords do not match.");
            $salt = generateSalt();
            $pass = hashPassword($_POST["pass"],$salt,$generalSalt);
            $email = strtolower($_POST["email"]);
            //$activation_kay = hash('crc32',$meeru); ****WE DON'T USE THIS RIGHT NOW
            $timezone = $_POST["tzdropdown"];

            //Check if the email is already in use
            $emailInUseQuery = $db->prepare("SELECT email FROM track_users WHERE email=?");
            $emailInUseQuery->execute(array($email));
            $emailInUseRow = $emailInUseQuery->fetch(); //If this query returns any rows, then the email is already in the DB

            if($emailInUseRow != false)
                die("That email is already in use.");

            $addUserQuery = $db->prepare("INSERT INTO track_users (email,password,salt,timezone) VALUES (?,?,?,?)");
            $addUserQuery->execute(array($email,$pass,$salt,$timezone));

           
            echo "You've successfully registered. <a href='../login'>Click here to go log in.</a>";
        }
    }
    else { //User failed the ayah test
        die("You screwed up on the Human Verification Test.");
    }
}
else {
    echo "<link rel='stylesheet' type='text/css' href='../tracka/index.css'>";
echo '<b>Registerin\'</b><br>
<form action="index.php" method="post">
	Email Address: <input id="mail" name="email" maxlength="50" placeholder="example@yahoo.com"/> <br>
	Password: <input type="password" id="password" name="pass" maxlength="30"/> <br>
	Confirm Password: <input type="password" id="password" name="confirmpass" maxlength="30"/> <br>
	Timezone: <select name="tzdropdown">
	<option value="Pacific/Kwajalein">(UTC -12:00) International Date Line West</option>
	<option value="Pacific/Samoa">(UTC -11:00) Midway Island, Samoa</option>
	<option value="Pacific/Honolulu">(UTC -10:00) Hawaii</option>
	<option value="America/Anchorage">(UTC -9:00) Alaska</option>
	
	<option value="America/Los_Angeles" selected="selected">(UTC -8:00) Pacific Time (US &amp; Canada); Tijuana</option>
	<option value="America/Denver">(UTC -7:00) Mountain Time (US &amp; Canada)</option>
	<option value="US/Arizona">(UTC -7:00) Arizona</option>
	<option value="Mexico/BajaNorte">(UTC -7:00) Chihuahua, La Paz, Mazatlan</option>
	<option value="America/Chicago">(UTC -6:00) Central Time (US &amp; Canada)</option>
	
	<option value="America/Costa_Rica">(UTC -6:00) Central America</option>
	<option value="Mexico/General">(UTC -6:00) Guadalajara, Mexico City, Monterrey</option>
	<option value="Canada/Saskatchewan">(UTC -6:00) Saskatchewan</option>
	<option value="America/New_York">(UTC -5:00) Eastern Time (US &amp; Canada)</option>
	<option value="America/Bogota">(UTC -5:00) Bogota, Lima, Quito</option>
	
	<option value="America/Indiana/Indianapolis">(UTC -5:00) Indiana (East)</option>
	<option value="Canada/Eastern">(UTC -4:00) Atlantic Time (Canada)</option>
	<option value="America/Caracas">(UTC -4:00) Caracas, La Paz</option>
	<option value="America/Santiago">(UTC -4:00) Santiago</option>
	<option value="America/St_Johns">(UTC -3:30) Newfoundland</option>
	<option value="Canada/Atlantic">(UTC -3:00) Brasilia, Greenland</option>
	
	<option value="America/Buenos_Aires">(UTC -2:00) Buenos Aires, Georgetown</option>
	<option value="Atlantic/Cape_Verde">(UTC -1:00) Cape Verde Is.</option>
	<option value="Atlantic/Azores">(UTC -1:00) Azores</option>
	<option value="Africa/Casablanca">(UTC) Casablanca, Monrovia</option>
	<option value="Europe/Dublin">(UTC) Greenwich Mean Time : Dublin, Edinburgh, London</option>
	<option value="Europe/Amsterdam">(UTC +1:00) Amsterdam, Berlin, Rome, Stockholm, Vienna</option>
	
	<option value="Europe/Prague">(UTC +1:00) Belgrade, Bratislava, Budapest, Prague</option>
	<option value="Europe/Paris">(UTC +1:00) Brussels, Copenhagen, Madrid, Paris</option>
	<option value="Europe/Warsaw">(UTC +1:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
	<option value="Africa/Bangui">(UTC +1:00) West Central Africa</option>
	<option value="Europe/Istanbul">(UTC +2:00) Athens, Beirut, Bucharest, Cairo, Istanbul</option>
	<option value="Asia/Jerusalem">(UTC +2:00) Harare, Jerusalem, Pretoria</option>
	
	<option value="Europe/Kiev">(UTC +2:00) Helsinki, Kiev, Riga, Sofia, Tallinn, Vilnius</option>
	<option value="Asia/Riyadh">(UTC +3:00) Kuwait, Nairobi, Riyadh</option>
	<option value="Europe/Moscow">(UTC +3:00) Baghdad, Moscow, St. Petersburg, Volgograd</option>
	<option value="Asia/Tehran">(UTC +3:30) Tehran</option>
	<option value="Asia/Muscat">(UTC +4:00) Abu Dhabi, Muscat</option>
	<option value="Asia/Baku">(UTC +4:00) Baku, Tbilsi, Yerevan</option>
	
	<option value="Asia/Kabul">(UTC +4:30) Kabul</option>
	<option value="Asia/Yekaterinburg">(UTC +5:00) Yekaterinburg</option>
	<option value="Asia/Karachi">(UTC +5:00) Islamabad, Karachi, Tashkent</option>
	<option value="Asia/Calcutta">(UTC +5:30) Chennai, Calcutta, Mumbai, New Delhi</option>
	<option value="Asia/Katmandu">(UTC +5:45) Katmandu</option>
	<option value="Asia/Almaty">(UTC +6:00) Almaty, Novosibirsk</option>
	
	<option value="Asia/Dhaka">(UTC +6:00) Astana, Dhaka, Sri Jayawardenepura</option>
	<option value="Asia/Rangoon">(UTC +6:30) Rangoon</option>
	<option value="Asia/Bangkok">(UTC +7:00) Bangkok, Hanoi, Jakarta</option>
	<option value="Asia/Krasnoyarsk">(UTC +7:00) Krasnoyarsk</option>
	<option value="Asia/Hong_Kong">(UTC +8:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
	<option value="Asia/Irkutsk">(UTC +8:00) Irkutsk, Ulaan Bataar</option>
	
	<option value="Asia/Singapore">(UTC +8:00) Kuala Lumpar, Perth, Singapore, Taipei</option>
	<option value="Asia/Tokyo">(UTC +9:00) Osaka, Sapporo, Tokyo</option>
	<option value="Asia/Seoul">(UTC +9:00) Seoul</option>
	<option value="Asia/Yakutsk">(UTC +9:00) Yakutsk</option>
	<option value="Australia/Adelaide">(UTC +9:30) Adelaide</option>
	<option value="Australia/Darwin">(UTC +9:30) Darwin</option>
	
	<option value="Australia/Brisbane">(UTC +10:00) Brisbane, Guam, Port Moresby</option>
	<option value="Australia/Canberra">(UTC +10:00) Canberra, Hobart, Melbourne, Sydney, Vladivostok</option>
	<option value="Asia/Magadan">(UTC +11:00) Magadan, Soloman Is., New Caledonia</option>
	<option value="Pacific/Auckland">(UTC +12:00) Auckland, Wellington</option>
	<option value="Pacific/Fiji">(UTC +12:00) Fiji, Kamchatka, Marshall Is.</option>
	</select>';
    echo $ayah->getPublisherHTML();
    echo '<br>
	<input type="submit" value="Heyoo" name="save" />
</form>
By registering, you agree that everything that happens is your fault, including, but not limited to, the Holocaust,<br>
the dinosaurs dying out, Japan getting nuked, and my servers getting hacked into. I can\'t be held liable for your stupidity.';
}