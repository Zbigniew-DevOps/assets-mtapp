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
        $button = 'hidden';
        $show = 'hidden';
        $nodata = '<font color="#FF0000">Sorry taki mamy klimat, że... nie masz uprawnień do dodawania kontraktów!!!</font>';
        }
if ($accounttype == "b") {
        $type = 'Manager';
        $show = 'text';
        $button = 'submit';
        }
if ($accounttype == "c") {
        $type = 'SuperAdmin';
        $show = 'text';
        $button = 'submit';
        }
if ($accounttype == "d") {
        $type = 'TechManager';
        $button = 'hidden';
        $show = 'hidden';
        $nodata = '<font color="#FF0000">Sorry taki mamy klimat, że... nie masz uprawnień do dodawania kontraktów!!!</font>';
        }

// Check if user is logged in
if (!isset($_SESSION['id'])) {
   header("Location: login.php");
} else {
    if (($accounttype == "c") or ($accounttype == "b")){

$errorMsg = '';
$showitem = 1;

// Check which button was clicked
if(isset($_GET['new'])) {
 $new = $_GET['new'];

if (!$new == 0) {
    header("Location: add_user.php");
 } else {
    header("Location: add_user_mac.php");

 }
 }
  }//Close if $_POST
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

<title>Wybierz usera</title>

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
        <form action="add_client.php" metod="post" enctype="multipart/form-data">
        <table border="0" class="searchbox">
<?php
if (!$showitem == 0) {
        echo '<p align="center"><h2>Wprowadzasz nowego klienta ANW</h2></p>
        <tr>
            <td class="cell_ll">Nowy klient?: </td>
            <td class="cell_r"><input name="new" type="radio" value="1" onclick="javascript: submit()" /><font color="#004800">   Tak  </font>
                               <input name="new" type="radio" value="0" onclick="javascript: submit()" /><font color="#D70000">   Nie  </font></td>
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
