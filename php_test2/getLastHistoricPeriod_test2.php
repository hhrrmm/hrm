<?php

/*
** Used to retrieve the ID and name of last historical period
*/
include 'MySQLConnectionDataProvider.php';

$credentials = new MySQLConnectionDataProvider();

$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
mysql_select_db($credentials->DB);

$query  = "SELECT historyEnd as TimeID, TimeName
			FROM ConsultingMQ.hr_indicator_test2, ConsultingMQ.hr_timeID_test2
			WHERE ConsultingMQ.hr_indicator_test2.historyEnd = ConsultingMQ.hr_timeID_test2.TimeID
			and IndicatorID = 13
			AND ConsultingMQ.hr_indicator_test2.IndicatorType=1";
/*$query  = "SELECT ConsultingMQ.hr_lastHistoric_period_test2.TimeID, TimeName "
		. " FROM ConsultingMQ.hr_lastHistoric_period_test2, ConsultingMQ.hr_timeID_test2 "
		. "WHERE ConsultingMQ.hr_lastHistoric_period_test2.TimeID = ConsultingMQ.hr_timeID_test2.TimeID "
		. " AND ConsultingMQ.hr_lastHistoric_period_test2.PeriodType=1";*/
		
$result = mysql_query($query);

$answer = "<entries>";

if ($result) {
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$answer .= "<entry>"
				.  "<timeID>".$line['TimeID']."</timeID>"
				.  "<period>".$line['TimeName']."</period>"
				.  "</entry>";
	}
}
else {
	echo "DB_Error";
	exit;
}

$answer .= "</entries>";

echo $answer;

?>