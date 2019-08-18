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
    if ($accounttype == "c") {

// Database search process
include_once "connect_to_mysqli_snst.php";
include_once "connect_to_mysqli_rad123_12_122.php";
include_once "connect_to_mysqli_rad123_11_123.php";

 $itemshow1 = 1;
 $itemshow2 = 0;
 $itemshow3 = 0;
 $itemshow4 = 0;
 $itemshow5 = 0;
 $radmsg = 0;

 $name1 = 'selectcontr';
 $button1 = 'submit';
 $buttonvalue1 = 'Wybierz';
 $showbutton1 = 1;
 $idb1 = 'scriptsubmitgreen';

 $listcontr = "SELECT * FROM snst.contract ORDER BY target";
 $resultlist = $mysqlist->query($listcontr);

// Check which button was clicked
if(isset($_POST['selectcontr'])) {

 $itemshow1 = 0;
 $itemshow4 = 0;
 $itemshow5 = 0;
 $radmsg = 0;

 $name1 = '';
 $name2 = '';
 $name3 = 'selectst';
 $button1 = '';
 $button2 = '';
 $button3 = 'submit';
 $buttonvalue1 = '';
 $buttonvalue2 = '';
 $buttonvalue3 = 'Wybierz';
 $showbutton1 = 0;
 $showbutton2 = 0;
 $idb1 = '';
 $idb2 = '';
 $idb3 = 'scriptsubmitgreen';

 $seltarget = $_POST['seltarget'];

 // Define ST owner
 $group1 = 'st'; // AFOST
 $grst1 = 'afost';
 $group2 = 'itsast'; // ITSAST
 $grst2 = 'itsast';
 $group3 = 'stextprojects';  // EXTST
 $grst3 = 'afost';

 $orderpipi = "
 SELECT ip FROM snst.$group1 LEFT JOIN snst.ip ON $group1.$grst1=ip.st WHERE $group1.target='$seltarget'
 UNION ALL
 SELECT ip FROM snst.$group2 LEFT JOIN snst.ip ON $group2.$grst2=ip.st WHERE $group2.target='$seltarget'
 UNION ALL
 SELECT ip FROM snst.$group3 LEFT JOIN snst.ip ON $group3.$grst3=ip.st WHERE $group3.target='$seltarget'
 ORDER BY 'loc'";
 $resultpipi = $mysqlist->query($orderpipi);

 $storeip = array();
 while ($rowpipi = mysqli_fetch_assoc($resultpipi)) {
    $storeip[] = $rowpipi['ip'];
 }
//  print_r($storeip);

 $nasrows = count($storeip);
 $nasips = "'" .implode("','",array_values($storeip)). "'";

 $ordernasq        = "SELECT * FROM sdnauth1.nas WHERE nasname IN ($nasips)";
 $ordernasqold1    = "SELECT * FROM sdnauth1.nas WHERE nasname IN ($nasips)";
 $ordernasqold2    = "SELECT * FROM sdnauth1.nas WHERE nasname IN ($nasips)";
 $resultnasq1      = $mysqli10225->query($ordernasq);
 $resultnasq2      = $mysqli10226->query($ordernasq);
 $num_rows_nasipst = $resultnasq1->num_rows;

 if ($num_rows_nasipst > 0){
 $itemshow2 = 1;
 $itemshow3 = 1;
 $showbutton3 = 1;
 } else {
 $itemshow2 = 0;
 $itemshow3 = 0;
 $showbutton3 = 0;
 $radmsg = 1;
 $radcheck = '<font color="#FF0000"><h2>W bazie NAS nie znaleziono danych dla wybranego kontraktu</h2>Spr&#243;buj ponownie... </font><br />Wr&#243;&#263; do strony wyszukiwania.';
 }

} else if(isset($_POST['selectst'])) {

 $itemshow1 = 0;
 $itemshow2 = 0;
 $itemshow3 = 0;
 $itemshow4 = 1;
 $radmsg = 0;

 $name1 = '';
 $button1 = 'hidden';
 $buttonvalue1 = '';
 $idb1 = 'scriptsubmitgreen';

 $name2 = 'writebill';
 $button2 = 'submit';
 $buttonvalue2 = 'Pobierz wynik';
 $idb2 = 'scriptsubmitgreen';
 $sello = $_POST['selip'];
 $nasello = explode(":", $sello);
   $selip = $nasello['0'];
   $loclabel = $nasello['1'];
 $inputField1 = $_POST['inputField1'];
 $inputField2 = $_POST['inputField2'];

// echo $inputField1.'/'.$inputField2;

 $orderact201907 = "SELECT * FROM sdnacc201907.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 $orderact1 = "SELECT * FROM sdnacc1.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 $orderact2 = "SELECT * FROM sdnacc2.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 
// Base 10.225
 $resultact1010225ac1 = $mysqli1010225->query($orderact1);
 $resultact1010225ac2 = $mysqli1010225->query($orderact2);

// Base 100.10.225
 $resultact10010225_07  = $mysqli10225->query($orderact201907);
 $resultact10010225_08  = $mysqli10225->query($orderact201908);
 $resultact10010225_09  = $mysqli10225->query($orderact201909);

// Base 100.10.226
 $resultact10010226_07  = $mysqli10226->query($orderact201907);
 $resultact10010226_08  = $mysqli10226->query($orderact201908);
 $resultact10010226_09  = $mysqli10226->query($orderact201909);

// Base 100.20.225
 $resultact10020225ac1 = $mysqli20225->query($orderact1);
 $resultact10020225_08 = $mysqli20225->query($orderact201908);
 $resultact10020225_09 = $mysqli20225->query($orderact201909);

// Base 100.20.226
 $resultact10020226_07 = $mysqli20226->query($orderact201907);
 $resultact10020226_08 = $mysqli20226->query($orderact201908);
 $resultact10020226_09 = $mysqli20226->query($orderact201909);

// Num rows Base 10.225
 $num_rows_rec1010225ac1 = $resultact1010225ac1->num_rows;
 $num_rows_rec1010225ac2 = $resultact1010225ac2->num_rows;

// Num rows Base 100.10.225
 $num_rows_rec10010225_07  = $resultact10010225_07->num_rows;
 $num_rows_rec10010225_08  = $resultact10010225_08->num_rows;
 $num_rows_rec10010225_09  = $resultact10010225_09->num_rows;

// Num rows Base 100.10.226
 $num_rows_rec10010226_07  = $resultact10010226_07->num_rows;
 $num_rows_rec10010226_08  = $resultact10010226_08->num_rows;
 $num_rows_rec10010226_09  = $resultact10010226_09->num_rows;

// Num rows Base 100.20.225
 $num_rows_rec10020225ac1 = $resultact10020225ac1->num_rows;
 $num_rows_rec10020225_08 = $resultact10020225_08->num_rows;
 $num_rows_rec10020225_09 = $resultact10020225_09->num_rows;

// Num rows Base 100.20.226
 $num_rows_rec10020226_07 = $resultact10020226_07->num_rows;
 $num_rows_rec10020226_08 = $resultact10020226_08->num_rows;
 $num_rows_rec10020226_09 = $resultact10020226_09->num_rows;



// echo ' NumRec: ' .$num_rows_rec1010225ac1. ' / ' .$num_rows_rec1010225ac1;

$resultact = $num_rows_rec1010225ac1 + $num_rows_rec1010225ac2 + $num_rows_rec10010225_07 + $num_rows_rec10010225_08 + $num_rows_rec10010225_09 + $num_rows_rec10010226_07
           + $num_rows_rec10010226_08 + $num_rows_rec10010226_09 + $num_rows_rec10020225ac1 + $num_rows_rec10020225_08 + $num_rows_rec10020225_09 +$num_rows_rec10020226_07
           + $num_rows_rec10020226_08 + $num_rows_rec10020226_09;

 if ($resultact > 0) {
  $showbutton1 = 0;
  $showbutton2 = 1;
  $itemshow5   = 1;
 } else {
  $showbutton1 = 0;
  $showbutton2 = 0;
  $itemshow5   = 1;
 }

} else if(isset($_POST['writebill'])) {

 $nasset = $_POST['nasset'];
 $nasellwrite = explode(":", $nasset);
   $selip = $nasellwrite['0'];
   $loclabel = $nasellwrite['1'];
   $inputField1 = $nasellwrite['2'];
   $inputField2 = $nasellwrite['3'];

 $orderact201907 = "SELECT * FROM sdnacc201907.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 $orderact1 = "SELECT * FROM sdnacc1.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 $orderact2 = "SELECT * FROM sdnacc2.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 $orderact082015 = "SELECT * FROM sdnacc201908.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";
 $orderact012016 = "SELECT * FROM sdnacc201909.radacct WHERE nasipaddress='$selip'
 AND (acctstarttime BETWEEN '$inputField1 00:00:00' AND '$inputField2 23:59:59') ORDER BY acctstarttime";

// Base 10.225
 $resultact1010225ac1 = $mysqli1010225->query($orderact1);
 $resultact1010225ac2 = $mysqli1010225->query($orderact2);

// Base 100.10.225
 $resultact10010225_07  = $mysqli10225->query($orderact201507);
 $resultact10010225_08  = $mysqli10225->query($orderact201908);
 $resultact10010225_09  = $mysqli10225->query($orderact201909);

// Base 100.10.226
 $resultact10010226_07  = $mysqli10226->query($orderact201507);
 $resultact10010226_08  = $mysqli10226->query($orderact201908);
 $resultact10010226_09  = $mysqli10226->query($orderact201909);

// Base 10.100.20.225
 $resultact10020225ac1 = $mysqli20225->query($orderact1);
 $resultact10020225_08 = $mysqli20225->query($orderact201908);
 $resultact10020225_09 = $mysqli20225->query($orderact201909);

// Base 10.100.20.226
 $resultact10020226_07 = $mysqli20226->query($orderact201507);
 $resultact10020226_08 = $mysqli20226->query($orderact201908);
 $resultact10020226_09 = $mysqli20226->query($orderact201909);

// Num rows Base 10.10.10.225
 $num_rows_rec1010225ac1 = $resultact1010225ac1->num_rows;
 $num_rows_rec1010225ac2 = $resultact1010225ac2->num_rows;

// Num rows Base 10.100.10.225
 $num_rows_rec10010225_07  = $resultact10010225_07->num_rows;
 $num_rows_rec10010225_08  = $resultact10010225_08->num_rows;
 $num_rows_rec10010225_09  = $resultact10010225_09->num_rows;

// Num rows Base 10.100.10.226
 $num_rows_rec10010226_07  = $resultact10010226_07->num_rows;
 $num_rows_rec10010226_08  = $resultact10010226_08->num_rows;
 $num_rows_rec10010226_09  = $resultact10010226_09->num_rows;

// Num rows Base 10.100.20.225
 $num_rows_rec10020225ac1 = $resultact10020225ac1->num_rows;
 $num_rows_rec10020225_08 = $resultact10020225_08->num_rows;
 $num_rows_rec10020225_09 = $resultact10020225_09->num_rows;

// Num rows Base 100.20.226
 $num_rows_rec10020226_07 = $resultact10020226_07->num_rows;
 $num_rows_rec10020226_08 = $resultact10020226_08->num_rows;
 $num_rows_rec10020226_09 = $resultact10020226_09->num_rows;

$resultact = $num_rows_rec1010225ac1 + $num_rows_rec1010225ac2 + $num_rows_rec10010225_07 + $num_rows_rec10010225_08 + $num_rows_rec10010225_09 + $num_rows_rec10010226_07
           + $num_rows_rec10010226_08 + $num_rows_rec10010226_09 + $num_rows_rec10020225ac1 + $num_rows_rec10020225_08 + $num_rows_rec10020225_09 +$num_rows_rec10020226_07
           + $num_rows_rec10020226_08 + $num_rows_rec10020226_09;

//echo $selip. ' ' .$inputField1. ' ' .$inputField2;

ob_end_clean();

if ($resultact > 0) {
        $acctresults = "Host(MAC)".","."MAC (CSID)".","."Czas pocz.".","."Czas kon.".","."Czas sesji,"."Dane in (oct.),"."Dane out (oct.)"."\r\n"; //note the comma here
    while ($row = $resultact1010225ac1->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact1010225ac2->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10010225_07->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10010225_08->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10010225_09->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10010226_07->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10010226_08->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10010226_09->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10020225ac1->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10020225_08->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10020225_09->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10020226_07->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10020226_08->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
    while ($row = $resultact10020226_09->fetch_array()) {
        $acctresults .= $row["username"] . "," . $row["callingstationid"] . "," . $row["acctstarttime"]. "," . $row["acctstoptime"]. "," . $row["acctsessiontime"]. "," . $row["acctinputoctets"]. "," . $row["acctoutputoctets"]. "\r\n"; //
    }
} else {
        $acctresults = "Nie znaleziono danych w bazach AFORTE..."; //note the comma here
}
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=" . $loclabel.'_'. $inputField1.'_'. $inputField2. ".csv");
print $acctresults;

   $changeuser = $_SESSION['user'];
   $changedate = date("Y-m-d H:i");
   $changetype = 'Bill created';
   $changecont = ' NAS: ' .$nasset;

    // Register a change in a changelog
    //$sql = mysqli_query("INSERT INTO sdnadmin.userlog (user, date, type, content) VALUES ('$changeuser','$changedate','$changetype','$changecont')", $radst) or trigger_error();

 mysqli_close();
 exit();
// header("Location: sukces.php?item=delnas");
  } // Close button check
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

<title>Szukaj danych ACC IP</title>

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
                    target:"inputField1",
                    dateFormat:"%Y-%m-%d"
                    })
                    new JsDatePick({
                    useMode:2,
                    target:"inputField2",
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
<form action="accip_search.php" method="post">
<table border="0" class="searchbox">


<?php
if (!$itemshow1 == 0) {
    echo '<p align="center"><h2><font color="#863014">Szukasz danych billingowych NASa w sieci SDN</font></h2></p>
        <tr>
            <td align="center" colspan="2"><h3>Wybierz kontrakt</h3></td>
        </tr>
        <tr>
            <td style="width:30%" class="left_blue_light">Nazwa kontraktu: </td>
            <td style="font-size:90%" class="right_blue_light"><select name="seltarget">';

            while($data = $resultlist->fetch_array()) {
                $targetsel = $data["target"];
            echo "<option value=" .$targetsel. ">" .$targetsel. "</option>";
            }
            echo '</select></td>
        </tr>';
}
if (!$itemshow2 == 0){
    echo '<p align="center"><h2><font color="#863014">Wybierz &#378;r&#243;d&#322;o danych</font></h2></p><input name="hostsel" type="hidden" value="' .$seltarget. '" />
    <p align="center"><h3><font color="#125f78">i podaj zakresu wyszukiwania</font></h3></p>
    <table align="center" cellpadding="8" border="0">
        <tr>
            <td>Wybierz adres IP: </td>
            <td>Od dnia: </td>
            <td>Do dnia: </td>
            <td></td>
        </tr>
        <tr>
            <td style="font-size:90%" class="right_blue_light"><select name="selip">';
             while ($rowipnas1 = mysqli_fetch_array($resultnasq1)) {
                 $stoip1 = $rowipnas1['nasname'];
                 $stoloc1 = $rowipnas1['shortname'];
                 echo "<option value=" .$stoip1. ':' .$stoloc1. ">" .$stoip1. "</option>";
             }
            echo '</select></td>
            <td><input type="text" size="11" id="inputField1" name="inputField1" value="' .$_POST['inputField1']. '" /></td>
            <td><input type="text" size="11" id="inputField2" name="inputField2" value="' .$_POST['inputField2']. '" /></td>
            <td><input name="' .$name3. '" id="' .$idb3. '" type="' .$button3. '" value="' .$buttonvalue3. '" /></td>
        </tr></table>';
}
if (!$itemshow3 == 0) {
    echo '<p align="center"><h4><i>Elementy kontraktu: ' .$seltarget. '</i></h4></p><input name="selectedtarget" type="hidden" value="' .$seltarget. '" />
        <table align="center" cellpadding="8" border="1">
        <tr>
            <td>Lokalizacja</td>
            <td>Adres IP</td>
            <td>Producent</td>
            <td>Typ</td>
            <td>Sekret</td>
            <td>Numer seryjny</td>
        </tr>';
             while ($rowipnas2 = mysqli_fetch_array($resultnasq2)) {
                 $nasip = $rowipnas2['nasname'];
                 $nasloc = $rowipnas2['shortname'];
                 $nasvend = $rowipnas2['type'];
                 $nastype = $rowipnas2['community'];
                 $nassec = $rowipnas2['secret'];
                 $nassn = $rowipnas2['description'];
                echo ("<tr><td>$nasloc</td><td>$nasip</td><td>$nasvend</td><td>$nastype</td><td>$nassec</td><td>$nassn</td></tr>");
            }
    echo '</table>';
}
if (!$itemshow4 == 0){
    echo '<p align="center"><h2>Dane dla urz&#261;dzenia o adresie: <font color="#8B0A50">' .$selip. '</font></h2></p>
          <p align="center"><h3>Identyfikator: <font color="#8B0A50">' .$loclabel. '</font></h3></p>
          <p align="center"><h4>Zakres od: <font color="#8B0A50">' .$inputField1. '</font> do: <font color="#8B0A50">' .$inputField2. '</font></h4></p>
          <input name="nasset" type="hidden" value="' .$selip. ':' .$loclabel. ':' .$inputField1. ':' .$inputField2. '" />';
}
if (!$showbutton2 == 0) {
        echo '<tr>
                <td colspan="6"><p><input name="' .$name2. '" id="' .$idb2. '" type="' .$button2. '" value="' .$buttonvalue2. '" /></p></td>
            </tr></table>';
}
if (!$itemshow5 == 0){
    echo '<table align="center" cellpadding="8" border="1">';
    if (!$num_rows_rec10010225_07 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201907 100-10-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10010225_07.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201907 100-10-225</font></td></tr>';
    }
    if (!$num_rows_rec10010226_07 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201907 100-10-226</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10010226_07.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201907 100-10-226</font></td></tr>';
    }
    if (!$num_rows_rec10010225_08 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201908 100-10-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10010225_08.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201908 100-10-225</font></td></tr>';
    }
    if (!$num_rows_rec10010226_08 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201908 100-10-226</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10010226_08.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201908 100-10-226</font></td></tr>';
    }
    if (!$num_rows_rec10010225_09 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201909 100-10-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10010225_01.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201909 100-10-225</font></td></tr>';
    }
    if (!$num_rows_rec10010226_09 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201909 100-10-226</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10010226_01.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201909 100-10.226</font></td></tr>';
    }
    if (!$num_rows_rec10020225ac1 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN AC1 100-20-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10020225ac1.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN AC1 100-20-225</font></td></tr>';
    }
    if (!$num_rows_rec10020225_08 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201908 100-20-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10020225_08.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201908 100-20-225</font></td></tr>';
    }
    if (!$num_rows_rec10020225_09 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201901 100-20-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10020225_01.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201901 100-20-225</font></td></tr>';
    }
    if (!$num_rows_rec10020226_07 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201907 100-20-226</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10020226_07.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201907 100-20-226</font></td></tr>';
    }
    if (!$num_rows_rec10020226_08 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201908 100-20-226</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10020226_08.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201908 100-20-226</font></td></tr>';
    }
    if (!$num_rows_rec10020226_09 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN 201909 100-20-226</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec10020226_01.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN 201601 100-20-226</font></td></tr>';
    }
    if (!$num_rows_rec1010225ac1 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN AC1 10-10-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec1010225ac1.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN AC1 10-10-225</font></td></tr>';
    }
    if (!$num_rows_rec1010225ac2 == 0) {
       echo '<tr><td align="center"><b>Dane z bazy:</b><font color="#863014"> SDN AC2 10-10-225</font></td><td align="center"><b>Liczba rekordów: </b><font color="#863014">'.$num_rows_rec1010225ac2.'</font></td></tr>';
    } else {
       echo '<tr><td colspan="2">Brak danych w bazie: <font color="#863014">SDN AC2 10-10-225</font></td></tr>';
    }


    //while ($rowact = mysqli_fetch_array($resultact10225)) {
            //    echo ("<tr><td>$rowact[3]</td><td>$rowact[18]</td><td>$rowact[9]</td><td>$rowact[10]</td><td>$rowact[11]</td><td>$rowact[15]</td><td>$rowact[16]</td></tr>");
            //}
            // while ($rowact = mysqli_fetch_array($resultact10226)) {
            //    echo ("<tr><td>$rowact[3]</td><td>$rowact[18]</td><td>$rowact[9]</td><td>$rowact[10]</td><td>$rowact[11]</td><td>$rowact[15]</td><td>$rowact[16]</td></tr>");
            //}
    echo '</table>';
}
if (!$radmsg == 0) {
        echo '<tr>
            <td style="padding: 3%" colspan="3" align="center">' .$radcheck. '</td>
        </tr>';
}
if (!$showbutton1 == 0) {
        echo '<tr>
                <td colspan="6"><p><input name="' .$name1. '" id="' .$idb1. '" type="' .$button1. '" value="' .$buttonvalue1. '" /></p></td>
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
