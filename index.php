<?php
session_start(); // Must start session first thing
// See if they are a logged in member by checking Session data
$toplinks = "";
if (isset($_SESSION['id'])) {
	// Put stored session variables into local php variable
    $userid = $_SESSION['id'];
    $username = $_SESSION['user'];
	$toplink1 = '<a href="member_profile.php?id=' . $userid . '">' . $user . '</a>'; 
	$toplink2 = '<a href="menu.php">Menu</a>';
	$toplink3 = '<a href="logout.php">Wylogowanie</a>';
} else {
	$toplink1 = '<a href="join_form.php">Rejestracja</a>';
	$toplink2 = '<a href="login.php">Logowanie</a>';
}
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

<title>AfoAuth login page</title>

	<script>
		$(function() {
			var pull 		= $('#pull');
				menu 		= $('nav ul');
				menuHeight	= menu.height();

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
</head>

<body class="blue">
	<nav class="clearfix">
		<ul class="clearfix">
			<li><?php echo $toplink1; ?></li>
			<li><?php echo $toplink2; ?></li>
			<li><?php echo $toplink3; ?></li>
		</ul>
		<a href="#" id="pull">Menu</a>
	</nav>

<div style="padding:12px">
  <img src="data:image/png;base64 ... wsad_obrazka" id="logo" alt="450x200"></a>
  <h1 class="slogan1">Witaj we Freelancers SDN Network!</h2>
  <p class="slogan1">Zaloguj si&#281; lub zarejestruj, aby korzysta&#263; z systemu.</p>
</div>

<div><a id="footer" href="http://www.lepsza.strona.mocy">Powered by AZJA Developers  &copy; 2007-2019 Zbigniew Jakubowski, All Rights Reserved</a></div>

</body>
</html>
