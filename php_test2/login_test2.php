<?php

/*
** Used to verify login passwords
*/
include 'MySQLConnectionDataProvider.php';

$usr = $_GET['usr']; $pw = $_GET['pw'];

$credentials = new MySQLConnectionDataProvider();

$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
mysql_select_db($credentials->DB);

$pass ="";

$query = "SELECT pw FROM ConsultingMQ.users WHERE userName=".$usr;

$result = mysql_query($query);

if ($query) {
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$pass = $row['pw'];
	}
	
	if ($pw == $pass) {
		echo "Success";
	}
	else {
		echo "Failed login";
	}
	//echo $pass;
	
}
else {
	echo "DB_Error";
	exit;
}

?>