 <?php 
/* 
1: "die()" will exit the script and show an error statement if something goes wrong with the "connect" or "select" functions. 
2: A "mysql_connect()" error usually means your username/password are wrong 
3: A "mysql_select_db()" error usually means the database does not exist.
*/

$db_host = "10.123.12.123";
$db_username = "mainPortalAdmin";
$db_pass = "takiTamPassword";
//$db_name1 = "terradmin";
//$db_name2 = "terrauth";

// Run the actual connection here 
$raddbx1 = mysql_connect("$db_host","$db_username","$db_pass") or die ("could not connect to mysql");
//mysql_select_db("$db_name1") or die ("no database");
//mysql_select_db("$db_name2") or die ("no database");
?>
