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

$name = 'houselo';
$button = 'submit';
$buttonvalue = 'Zatwierd&#378;';
$showbutton = 1;
$id = 'minisubmit';

// Database search precess
include_once "connect_to_mysqli_snst.php";
$resultcity = $mysqlist->query("SELECT cityname FROM snst.city ORDER BY cityname");
//$num_rows_building = mysqli_num_rows($resultbuilding);

if(isset($_POST['houselo'])) {

// Filter the posted variables
$buildname = preg_replace("/[^A-Z0-9_]/","",$_POST['buildname']); // filter everything but numbers, undercourse and capital letters
      $zip = preg_replace("/[^0-9-]/","",$_POST['zip']); // filter everything but numbers, undercourse and capital letters
   $street = $_POST['street'];
     $city = $_POST['city'];
echo $buildname. ' / ' .$street. ' / ' .$zip. ' / ' .$city;
// Check all fields are not empty
if((!$buildname) || (!$zip) || (!$street) || (!$city)){
            $errorMsg = "Nie wprowadzono wszystkich wymaganych danych!<br /><br />";
            if(!$buildname){
            $errorMsg .= "Wprowadź nazwę budynku";
            } else if(!$zip){
            $errorMsg .= "Podaj kod pocztowy";
            } else if(!$street){
            $errorMsg .= "Podaj ulicę";
            } else if(!$city){
            $errorMsg .= "Wybierz miasto";
            }
    } else {
    // Database duplicate Fields Check
    $sql_target_check = $mysqlist->query("SELECT id FROM snst.building WHERE name='$buildname' LIMIT 1");
        $target_check = $sql_target_check->num_rows;
    $changeuser = $_SESSION['user'];
    $changedate = date("Y-m-d H:i");
    $changetype = 'new building';
    $changecont = 'Budynek: ' .$buildname;
    if ($target_check > 0){
      $errorMsg = "<u>ERROR:</u><br />Podany budynek istnieje ju&#380; w bazie. Prosze wybra&#263; inn&#261; nazw&#281;.";
    } else {
    // Add user info into the database table, claim your fields then values
    $sql = $mysqlist->query("INSERT INTO snst.building (name, street, zip, city) VALUES ('$buildname','$street','$zip','$city')") or trigger_error();
    // Register a change in a changelog
    $sql = $mysqlist->query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')") or trigger_error();
}
         mysqli_close();
         $_SESSION['buildname'] = $buildname;
         header("Location: add_contr_end.php");
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

<title>Dodaj budynek</title>

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
        <form action="add_building.php" method="post" enctype="multipart/form-data">
        <table border="0" class="searchbox">
<?php
if (!$showitem == 0) {
        echo '<p align="center"><h2>Podaj adres nowego klienta (budynku)</h2></p>
        <h5 align="center"><i>Nazwa budynku powinna zawierać cyfry, wyłącznie duże litery,<br /> 
        łącznik "_" pomiędzy członami nazwy zamiast spacji i nie zawierać polskich znaków<br />- przykład BLOTNA_CENTER_2</i></h5>';
        if (!$errorMsg == 0) {
        echo '<tr>
             <td colspan="2"><h4 align="center"><font color="#890000">' .$errorMsg. '</font></h4></td>
        </tr>';
        }
        echo '<tr>
            <td class="cell_ll">Nazwa budynku: </td>
            <td class="cell_r"><input name="buildname" type="text" value="' .$buildname. '" /></td>
        </tr>

        <tr>
            <td class="cell_ll">Ulica: </td>
            <td class="cell_r"><input name="street" type="text" value="' .$street. '" /></td>
        </tr>

        <tr>
            <td class="cell_ll">Kod pocztowy: </td>
            <td class="cell_r"><input name="zip" size="4" type="text" value="' .$zip. '" /></td>
        </tr>
        <tr>
            <td class="cell_ll">Miasto: </td>
            <td class="cell_r"><select name="city">';
               while ($data = $resultcity->fetch_array()){
                    echo "<option value=" .$data["cityname"]. ">".$data["cityname"]."</option>";
              }
        echo '</select></td>
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
