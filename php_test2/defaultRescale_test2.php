<?php

/*
** Used to set rescale values to default
*/
include 'MySQLConnectionDataProvider.php';

$credentials = new MySQLConnectionDataProvider();

$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
mysql_select_db($credentials->DB);

$clean_query = "DELETE FROM ConsultingMQ.hr_rescale_test2 WHERE TimeID>0";
$fill_query = "INSERT INTO ConsultingMQ.hr_rescale_test2(TimeID, RescaleValue) SELECT TimeID, RescaleValue FROM ConsultingMQ.default_rescale";

$cl = mysql_query($clean_query);

if ($cl) {
	$fill = mysql_query($fill_query);
	
	if(!$fill) {
		echo "DB_Error";
		exit;
	}
}
else {
	echo "DB_Error";
	exit;
}

?>