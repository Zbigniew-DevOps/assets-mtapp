<?php
session_start(); // Must start session first thing
// See if they are a logged in member by checking Session data
$toplinks = "";
$accounttype = "";
if (isset($_SESSION['id'])) {
// Put stored session variables into local php variable
    $userid = $_SESSION['id'];
    $user = $_SESSION['user'];
    $accounttype = $_SESSION['accounttype'];
        $toplink1 = '<a href="logout.php">Wylogowanie</a>';
} else {
        $toplink1 = '<a href="join_form.php">Rejestracja</a>';
        $toplink2 = '<a href="login.php">Logowanie</a>';
}

if ($accounttype == "a") {
      $type = 'Reader';
}
if ($accounttype == "b") {
      $type = 'Manager';
}
if ($accounttype == "c") {
      $type = 'SuperAdmin';
}
if ($accounttype == "d") {
      $type = 'TechManager';
}

// Check if user is logged in
if (!isset($_SESSION['id'])) {
   header("Location: login.php");
} else {
    if (($accounttype == "c") or ($accounttype == "b")){

$errorMsg = '';
$showitem = 1;

$name = 'uselo';
$button = 'submit';
$buttonvalue = 'Zatwierd&#378;';
$showbutton = 1;
$id = 'minisubmit';

// Database search precess
include_once "connect_to_mysql_rad1.php";
include_once "connect_to_mysql_rad2.php";

if(isset($_POST['uselo'])) {

// Filter the posted variables
$accountname = preg_replace("/[^A-Z0-9_]/","",$_POST['accountname']); // filter everything but numbers, undercourse and capital letters
$username = $_POST['username']; 
$zip = preg_replace("/[^0-9-]/","",$_POST['zip']); // filter everything but numbers and dashes
$street = $_POST['street']; 
$city = $_POST['city'];
$floor = $_POST['floor'];
$nip = preg_replace("/[^0-9]/","",$_POST['nip']); // filter everything but numbers
echo $accountname. ' / ' .$street. ' / ' .$zip. ' / ' .$city;
// Check all fields are not empty
if((!$accountname) || (!$zip) || (!$street) || (!$city)){
            $errorMsg = "Nie wprowadzono wszystkich wymaganych informacji!<br /><br />";
            if(!$accountname){
            $errorMsg .= "Wprowadź nazwę klienta";
            } else if(!$zip){
            $errorMsg .= "Podaj kod pocztowy";
            } else if(!$street){
            $errorMsg .= "Podaj ulicę";
            } else if(!$city){
            $errorMsg .= "Wybierz miasto";
            }
    } else {
    // Database duplicate Fields Check
    $sql_target_check = mysql_query("SELECT id FROM sdncsm1.account WHERE account='$accountname' LIMIT 1");
    $target_check = mysql_num_rows($sql_target_check, $raddb1);
    $changeuser = $_SESSION['user'];
    $changedate = date("Y-m-d H:i");
    $changetype = 'new company';
    $changecont = 'Firma: ' .$username;
    if ($target_check > 0){
        $errorMsg = "<u>ERROR:</u><br />Podana konto u&#380;ytkownika istnieje ju&#380; w bazie. Prosze wybra&#263; inn&#261; nazw&#281;.";
    } else {
    // Add user info into the database table, claim your fields then values
    $sql = mysql_query("INSERT INTO sdncsm1.account (account, company, nip, street, zip, city, floor) VALUES ('$accountname','$username','$nip','$street','$zip','$city','$floor')", $raddb1) or die (mysql_error());
    $sql = mysql_query("INSERT INTO sdncsm1.account (account, company, nip, street, zip, city, floor) VALUES ('$accountname','$username','$nip','$street','$zip','$city','$floor')", $raddb2) or die (mysql_error());
    // Register a change in a changelog
    $sql = mysql_query("INSERT INTO sdncsm1.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')", $raddb1) or die (mysql_error());
    $sql = mysql_query("INSERT INTO sdncsm1.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')", $raddb2) or die (mysql_error());
}
         mysql_close();
         $_SESSION['accountname'] = $accountname;
         header("Location: add_user.php");
   } // Close button clicked
  } // Close else after missing vars check
 }// Close if user is valid
} // Close else if user is not loged in
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="SDN Network Assets">
<meta name="author" content="Zbigniew Jakubowski">
<link rel="stylesheet" href="css/normalize.css">
<link rel="stylesheet" href="css/style.css">
<link rel="shortcut icon" href="images/favicon.ico">

<link rel="stylesheet" type="text/css" media="all" href="jsDatePick_ltr.min.css" />
<script type="text/javascript" src="jquery.1.4.2.js"></script>
<script type="text/javascript" src="jsDatePick.jquery.min.1.3.js"></script>

<title>Dodaj konto</title>

        <script>
                $(function() {
                        var pull = $('#pull');
                                menu = $('nav ul');
                                menuHeight = menu.height();

                        $(pull).on('click', function(e) {
                                e.preventDefault();
                                menu.slideToggle();
                        });

                        $(window).resize(function(){
                        var w = $(window).width();
                        if(w > 320 && menu.is(':hidden')) {
                            menu.removeAttr('style');
                        }
                });
                });
        </script>
        <script type="text/javascript">
                window.onload = function(){
                    new JsDatePick({
                    useMode:2,
                    target:"inputField",
                    dateFormat:"%Y-%m-%d"
                    });
                };
        </script>


</head>
<body class="curves">
        <nav class="clearfix">
          <ul class="clearfix">
             <li><a href=" menu.php">Powr&#243;t</a></li>
             <li><?php echo $toplink1; ?></li>
             <li><?php echo $toplink2; ?></li>
          </ul>
        <a href="#" id="pull">Menu</a>
        </nav>

<div class="container">
        <form action="add_account.php" method="post" enctype="multipart/form-data">
        <table border="0" class="searchbox">
<?php
if (!$showitem == 0) {
        echo '<p align="center"><h2>Wprowad&#378; dane nowego konta</h2></p>
        <h5 align="center"><i>Nazwa klienta powinna zawiera&#263; cyfry, wy&#322;&#261;cznie du&#380;e litery,<br /> 
        &#322;&#261;cznik "_" pomi&#281;dzy cz&#322onami nazwy zamiast spacji i nie zawiera&#263; polskich znak&#243;w<br />- przyk&#322;ad JAN_KOWALSKI, MALECKI_RODEK etc.<br />
        <b>NIP wprowad&#378; bez spacji i &#322;&#261;cznik&#243;w</b></i></h5>';
        if (!$errorMsg == 0) {
        echo '<tr>
             <td colspan="2"><h4 align="center"><font color="#890000">' .$errorMsg. '</font></h4></td>
        </tr>';
        }
        echo '<tr>
            <td class="cell_ll">Nazwa konta: </td>
            <td class="cell_r"><input name="accountname" type="text" value="' .$accountname. '" /></td>
        </tr>
        <tr>
            <td class="cell_ll">Pe&#322;na nazwa klienta: </td>
            <td class="cell_r"><input name="username" type="text" value="' .$username. '" /></td>
        </tr>

        <tr>
            <td class="cell_ll">NIP: </td>
            <td class="cell_r"><input name="nip" size="10" type="text" value="' .$nip. '" /></td>
        </tr>
        <tr>
            <td class="cell_ll">Ulica: </td>
            <td class="cell_r"><input name="street" type="text" value="' .$street. '" /></td>
        </tr>
        <tr>
            <td class="cell_ll">Lokal: </td>
            <td class="cell_r"><input name="floor" size="10" type="text" value="' .$floor. '" /></td>
        </tr>
        <tr>
            <td class="cell_ll">Kod pocztowy: </td>
            <td class="cell_r"><input name="zip" size="4" type="text" value="' .$zip. '" /></td>
        </tr>
        <tr>
            <td class="cell_ll">Miasto: </td>
            <td class="cell_r"><select name="city">
                <option value="Warszawa">Warszawa</option>
                <option value="Poznań">Poznań</option>
                <option value="Kraków">Kraków</option>
            </select></td>
        </tr>';
}

if (!$showbutton == 0) {
        echo '<tr>
            <td colspan="2"><p><input name="' .$name. '" id="' .$id. '" type="' .$button. '" value="' .$buttonvalue. '" /></p></td>
        </tr>';
}
if (!$nodata == 0) {
        echo '<tr>
            <td style="padding: 3%" colspan="3" align="center">' .$nodata. '</td>
        </tr>';
}
?>
      </table>
    </form>

</div>



<div id="footer">
    <table class="table_footer">
        <tr>
            <td>  </td>
            <td class="cell_l"><a href="http://www.lepsza.strona.mocy">Powered by AZJA Developers  &copy; 2007-2019 Zbigniew Jakubowski, All Rights Reserved</a></td>
            <td class="cell_lf">Zalogowany jako:   <?php echo $_SESSION['user']; ?> / Upr.: <?php echo $type; ?></td>
        </tr>
    </table>
</div>

</body>
</html>
