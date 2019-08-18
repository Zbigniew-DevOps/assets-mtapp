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
	//$button = 'hidden';
	$show = 'hidden';
	}
if ($accounttype == "b") {
	$type = 'Manager';
	$show = 'text';
	//$button = 'submit';
	}
if ($accounttype == "c") {
	$type = 'SuperAdmin';
	$show = 'text';
	}
if ($accounttype == "d") {
	$type = 'TechManager';
	//$button = 'hidden';
	$show = 'hidden';
	//$nodata = '<font color="#FF0000">Sorry taki mamy klimat, że... nie masz uprawnień do dodawania kontraktów!!!</font>';
	}

// Check if user is logged in
if (!isset($_SESSION['id'])) {
   header("Location: login.php");
} else {
 if (!(($accounttype == "c") or ($accounttype == "d"))){
    header("Location: menu.php");
 } else {

    $button1 = 'submit';
    $errorMsg = '';
    $present1 = 'yes';

    // Prepare database query for equipment search
    include_once "connect_to_mysql_snst.php";
    $ordertypesel = "SELECT * FROM snst.hwprices ORDER BY vendor,vtype";
    $ordercontsel = "SELECT * FROM snst.contract ORDER BY target";

    $resulttypesel = mysql_query($ordertypesel);
    $resultcontsel = mysql_query($ordercontsel);

    $num_rows_typesel = mysql_num_rows($resulttypesel);
    $num_rows_contsel = mysql_num_rows($resultcontsel);

    // Check which button was clicked
    if(isset($_GET['typeselect'])) {
	    $button2 = 'submit';
	    $typeselected = $_GET['typesel'];
	    $type_vend  = explode(':', $typeselected);
	    $type = $type_vend['1'];
	    $vend = $type_vend['0'];
	    $price = $type_vend['2'];
	    $present1 = 'no';

	} else if(isset($_GET['addnewst'])) {
	    $typereq = $_GET["typereq"];
	    $req  = explode(':', $typereq);
		$invend = $req['0'];
		$intype = $req['1'];
		$inprice = $req['2'];
	    // Filter the posted variables
	    $sn = preg_replace("/[^A-Z0-9-]/","",$_GET['sn']); // filter everything but numbers, undercourse and capital letters
	    if(!$sn){
		$errorMsg = 'Nie wprowadzono numeru SN!<br /><br />';
	    } else {
	    // Database duplicate Fields Check
	    $sql_st_check = mysql_query("SELECT id FROM snst.st WHERE sn='$sn' LIMIT 1");
	    $st_check = mysql_num_rows($sql_st_check);
	    $changeuser = $_SESSION['user'];
	    $changedate = date("Y-m-d H:i");
	    $changetype = 'new ST added';
	    $changecont = 'Dodano ST: ' .$typereq. ' - ' .$sn;
	    if ($st_check > 0){
	        $errorMsg = '<u>ERROR:</u><br />Wprowadzane urz&#261;dzenie istnieje ju&#380; w bazie. Prosze wprowadzi&#263; inne urz&#261;dzenie.';
	    } else {
	    // Add sn info into the database table, claim your fields then values
	    $sql = mysql_query("INSERT INTO snst.st (sn, type, vendor, price) VALUES ('$sn','$intype','$invend','$inprice')") or die (mysql_error());
	    $ids = mysql_insert_id();
	    $present1 = 'no';
	    $idst = 'ST0' .$ids. 'A';
	    $sql = mysql_query("UPDATE snst.st SET afost='$idst' WHERE sn='$sn'") or die (mysql_error());
	    // Register a change in a changelog
	    $sql = mysql_query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')") or die (mysql_error());
	    $button3 = 'submit';
	    } // Close else after database write with new values
	    } // Close else after database duplicate field value checks
	} else if(isset($_GET['update'])) {
	    $button4 = 'submit';
	    $contsel = $_GET['contsel'];
	    $updatedst = $_GET['afost'];
	        $present1 = 'no';

	    $changeuser = $_SESSION['user'];
	    $changedate = date("Y-m-d H:i");
	    $changetype = 'free st decree';
	    $changecont = 'ST: ' .$updatedst. ' przydzielony do: ' .$contsel;

	    // Database update field
	    $sql = mysql_query("UPDATE snst.st SET target='$contsel' WHERE sdnst='$updatedst'") or die (mysql_error());
	    // Register a change in a changelog
	    $sql = mysql_query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')") or die (mysql_error());

	} else if(isset($_GET['stselected'])) {
	    $selst = $_GET['stloc'];
	    $selpref = $_GET['selpref'];
	    $selcity = $_GET['selcity'];
	    $targetlabel = $_GET['targetlabel'];
	    $selrole = $_GET['selrole'];
	    $selno = preg_replace("/[^1-9 ]/","",$_POST['selno']); // filter everything but numbers except zero
	    $newid = $selpref. '_' .$selcity. '_' .$targetlabel. '_' .$selrole . $selno;

	    $changeuser = $_SESSION['user'];
	    $changedate = date("Y-m-d H:i");
	    $changetype = 'st labeled';
	    $changecont = 'ST: ' .$selst. ' etykieta: ' .$selpref. '_' .$selcity. '_' .$targetlabel. '_' .$selrole . $selno;

	    // Database update fields
	    $sql = mysql_query("UPDATE snst.st SET loc='$newid' WHERE afost='$selst'") or die (mysql_error());
	    // Register a change in a changelog
	    $sql = mysql_query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')") or die (mysql_error());
	    mysql_close();
	    header("Location: sukces.php?item=newst");
	} // Close else after missing vars check and add button pressing
    }//Close if $_POST
 } // Close else if user is not loged in
} // Exit if account is authorized
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

<title>Dodaj recznie ST</title>

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

<div class="search_container">
<form action="add_st.php" metod="post" enctype="multipart/form-data">
<?php
    if($present1 == 'yes') {
	echo '<p align="center"><h2>Wprowad&#378; nowe urządzenie do bazy</h2></p>
	<a align="center"><i>Skontaktuj się z administratorem,<br /> jeśli nie znajdziesz nazwy wprowadzanego<br />urządzenia na liście sprzętu.</i></a>
	<p align="center">Numer SN należy wpisywać używając cyfr i wielkich liter (np. 02DA4BF76DB5)</p>
	<table border="0" class="searchbox">';
	if (!$errorMsg == 0){
		    echo '<tr><td colspan="2" class="tabletop_err">' .$errorMsg. '</td></tr>';
		}
	echo '<tr>
		    <td class="left_blue_light">Nazwa &#347;rodka: </td>
		    <td class="center_blue_light"><select name="typesel">';

		while ($data = mysql_fetch_array($resulttypesel)){
		    $typeseli = $data[2];
		    $sevendor = $data[1];
		    $devprice = $data[3];
		    echo "<option value=" .$sevendor. ":" .$typeseli. ":" .$devprice. ">" .$sevendor. ": " .$typeseli. "</option>";
		}
	echo '</select></td>
		    <td class="right_blue_light"><input name="typeselect" id="minisubmit" type="' .$button1. '" value="Wybierz" /></td>
	    </tr>
	</table>';
    }
    if (!$typeselected == 0){
        echo '<table border="0" class="searchbox">
	    <tr>
		<td colspan="3"><i><h2>Dodajesz &#347;rodek: ' .$vend. ' - ' .$type. '<input name="typereq" type="hidden" value="' .$vend. ':' .$type. ':' .$price. '" /></h2><i></td>
	    </tr>
	    <tr>
		<td class="left_blue_light">Numer seryjny (SN): </td>
		<td class="center_blue_light"><input name="sn" size="20" type="text" /></td>
		<td class="right_blue_light"><input name="addnewst" id="minisubmit" type="' .$button2. '" value="Dodaj" /></td>
	    </tr>
	    </table>';
    }
    if (!$idst == 0){
        echo '<table border="0" class="searchbox">
	    <tr>
		<td colspan="2"><h3>Dekretujesz: ' .$idst. '<input name="sdnst" type="hidden" value="' .$idst. '" /></td>
	    </tr>
	    <tr>
		<td class="left_blue_light">Do kontraktu: </td>
		<td class="center_blue_light"><select name="contsel">';

	    while ($data = mysql_fetch_array($resultcontsel)){
	        $contseli = $data[1];
		echo "<option value=" .$contseli. ">" .$contseli. "</option>";
	    }
	    echo '</select></td>
	    </tr>
	    <tr>
		<td colspan="2"><p><input name="update" id="minisubmit" type="' .$button3. '" value="Dekretuj ST" /></p></td>
	    </tr>
	</table>';
    }
    if (!$updatedst == 0) {
        echo '<p align="center"><h2>Nadaj identyfikator: </h2></p>
	    <p align="center"><h3>Kontrakt: ' .$contsel. '   Numer: ' .$updatedst. '<input name="stloc" type="hidden" value="' .$updatedst. '" /></h3></p>
	<table border="0" class="searchbox">
	    <tr>
		<td style="width:20%" class="cell_l">Etykieta: </td>
	        <td class="cell_r" style="width:15%"><select name="selpref">
		    <option value="SMB">SMB</option>
		    <option value="CORE">CORE</option>
		    <option value="SOHO">SOHO</option>
		    </select></td>
		<td class="cell_r" style="width:15%"><select name="selcity">
		    <option value="WAW">WAW</option>
		    <option value="POZ">POZ</option>
		    <option value="KRA">SOHO</option>
		    </select></td>
		<td style="width:50%">
		    <font color="#fff">' .$contsel. '</font><input name="targetlabel" type="hidden" value="' .$contsel. '" />
		</td>
		<td class="cell_r" style="width:15%"><select name="selrole">
		    <option value="R">R</option>
		    <option value="AP">AP</option>
		    <option value="APR">APR</option>
		    <option value="SW">SW</option>
		    </select></td>
		<td style="width:25%">
		    <input name="selno" type="text" size="4" value="" />
		</td>
	    </tr>
	    <tr>
		    <td colspan="6"><input name="stselected" id="minisubmit" type="' .$button4. '" value="Akceptuj" /></td>
	    </tr>
	</table>';
    }

?>
	<table border="0" class="searchbox">
	     <tr>
		    <td style="padding: 3%" colspan="3" align="center"><?php print $nodata; ?></td>
	    </tr>
	</table>
    </form>
</div>



<div id="footer">
    <table class="table_footer">
	<tr>
	    <td>  </td>
	    <td class="cell_l"><a href="http://www.lepsza.strona.mocy">Powered by AZJA Developers  &copy; 2017-2019 Zbigniew Jakubowski, All Rights Reserved</a></td>
	    <td class="cell_lf">Zalogowany jako:   <?php echo $_SESSION['user']; ?> / Upr.: <?php echo $type; ?></td>
	</tr>
    </table>
</div>

</body>
</html>
